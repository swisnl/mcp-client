<?php

namespace Swis\McpClient\Transporters;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;

use function React\Async\await;

use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use React\Stream\ReadableStreamInterface;
use Swis\McpClient\Exceptions\ConnectionFailedException;
use Swis\McpClient\Requests\InitializeRequest;
use Swis\McpClient\Requests\RequestInterface;

/**
 * StreamableHttpTransporter that extends SseTransporter for the MCP version 2025-03-26.
 * Handles the Mcp-Session-Id header for session management.
 */
class StreamableHttpTransporter extends SseTransporter
{
    /**
     * @var string|null The session ID for the current MCP connection
     */
    private ?string $sessionId = null;

    /**
     * Keep strong references to active response streams per request ID.
     *
     * @var array<string, ReadableStreamInterface>
     */
    private array $activeResponseStreams = [];

    /**
     * Per-request SSE parsing buffers.
     *
     * @var array<string, string>
     */
    private array $responseBuffers = [];

    public function initializeConnection(EventDispatcherInterface $eventDispatcher, array $capabilities, array $clientInfo, string $protocolVersion): array
    {
        // Streamable HTTP does not maintain a separate SSE connection, but it still needs
        // the dispatcher to forward per-request responses to the client.
        $this->eventDispatcher = $eventDispatcher;

        // For StreamableHttpTransporter, the request endpoint is the initial endpoint
        $this->requestEndpoint = $this->initialEndpoint;

        $initRequest = new InitializeRequest(
            capabilities: $capabilities,
            clientInfo: $clientInfo,
            protocolVersion: $protocolVersion
        );

        try {
            $serverInfo = [];

            await(
                $this
                    ->doSendRequest($initRequest)
                    ->then(function (ResponseInterface $result) use (&$serverInfo) {
                        $this->updateSessionIdFromResponse($result);
                        $serverInfo = @json_decode((string) $result->getBody(), true) ?: [];
                    })
                    ->then(fn () => register_shutdown_function(fn () => $this->loop->stop()))
                    ->then(fn () => register_shutdown_function([$this, 'disconnect']))
                    ->then(fn () => $this->afterInitialization())
            );

            $this->connected = true;

            return $serverInfo;
        } catch (\Throwable $e) {
            throw new ConnectionFailedException('Failed to connect to MCP server', 0, $e);
        }
    }

    public function sendRequest(RequestInterface $request): PromiseInterface
    {
        $requestId = $request->getId();

        return parent::sendRequest($request)
            ->then(function (ResponseInterface $response) use ($requestId) {
                $this->updateSessionIdFromResponse($response);
                $contentType = $this->normalizeContentType($response->getHeaderLine('Content-Type'));

                return match ($contentType) {
                    'text/event-stream' => $this->handleEventStreamResponse($requestId, $response),
                    'application/json' => $this->handleJsonResponse($requestId, $response),
                    default => throw new \RuntimeException('Unexpected Content-Type: ' . $response->getHeaderLine('Content-Type')),
                };
            });
    }

    public function disconnect(): void
    {
        foreach ($this->activeResponseStreams as $stream) {
            $stream->close();
        }

        $this->activeResponseStreams = [];
        $this->responseBuffers = [];

        parent::disconnect();
    }

    protected function handleJsonResponse(string $requestId, ResponseInterface $response): ResponseInterface
    {
        $payload = (string) $response->getBody();
        if ($payload === '') {
            return $response;
        }

        try {
            $decoded = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
            if (! is_array($decoded)) {
                throw new \RuntimeException('Decoded Streamable HTTP JSON response is not an array');
            }

            $this->logger->debug('Received Streamable HTTP JSON response', ['response' => $decoded, 'requestId' => $requestId]);
            $this->dispatchDecodedPayload($requestId, $decoded);
        } catch (\JsonException $e) {
            $this->logger->error('Failed to decode Streamable HTTP JSON response', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            throw new \RuntimeException('Failed to decode Streamable HTTP JSON response', 0, $e);
        }

        return $response;
    }

    protected function getDefaultHeaders(): array
    {
        $headers = parent::getDefaultHeaders();
        $headers['Accept'] = 'application/json,text/event-stream';

        if (isset($this->sessionId)) {
            $headers['Mcp-Session-Id'] = $this->sessionId;
        }

        return $headers;
    }

    /**
     * Keep per-request event streams alive and reject if they close before any response is dispatched.
     */
    private function handleEventStreamResponse(string $requestId, ResponseInterface $response): PromiseInterface
    {
        $stream = $response->getBody();
        if (! $stream instanceof ReadableStreamInterface) {
            throw new \RuntimeException('Invalid stream returned from Streamable HTTP request');
        }

        $this->activeResponseStreams[$requestId] = $stream;
        $this->responseBuffers[$requestId] = '';

        $deferred = new Deferred();
        $hasMatchingResponse = false;
        $settled = false;

        $stream->on('data', function (string $chunk) use ($requestId, $response, $deferred, $stream, &$hasMatchingResponse, &$settled): void {
            if ($settled) {
                return;
            }

            $normalizedChunk = str_replace("\r\n", "\n", $chunk);
            $this->responseBuffers[$requestId] = ($this->responseBuffers[$requestId] ?? '') . $normalizedChunk;
            $hasMatchingResponse = $this->processResponseBuffer($requestId) || $hasMatchingResponse;

            if (! $hasMatchingResponse) {
                return;
            }

            $settled = true;
            $this->cleanupResponseStream($requestId);
            $deferred->resolve($response);
            $stream->close();
        });

        $stream->on('error', function (\Throwable $e) use ($requestId, $deferred, &$settled): void {
            if ($settled) {
                return;
            }

            $settled = true;
            $this->logger->error('Streamable HTTP event stream error', [
                'requestId' => $requestId,
                'error' => $e->getMessage(),
            ]);
            $this->cleanupResponseStream($requestId);
            $deferred->reject($e);
        });

        $stream->on('close', function () use ($requestId, $response, $deferred, &$hasMatchingResponse, &$settled): void {
            if ($settled) {
                return;
            }

            $settled = true;
            $this->cleanupResponseStream($requestId);

            if (! $hasMatchingResponse) {
                $deferred->reject(new \RuntimeException("Event stream closed before response was received for request ID [$requestId]."));

                return;
            }

            $deferred->resolve($response);
        });

        return $deferred->promise();
    }

    /**
     * Process buffered SSE chunks for a single request stream and return true once the matching request ID is dispatched.
     */
    private function processResponseBuffer(string $requestId): bool
    {
        $didDispatchForRequest = false;

        while (isset($this->responseBuffers[$requestId]) && ($pos = strpos($this->responseBuffers[$requestId], "\n\n")) !== false) {
            $chunk = substr($this->responseBuffers[$requestId], 0, $pos);
            $this->responseBuffers[$requestId] = substr($this->responseBuffers[$requestId], $pos + 2);

            $data = $this->extractSseData($chunk);
            if ($data === null) {
                continue;
            }

            $didDispatchForRequest = $this->decodeAndDispatchSseData($requestId, $data) || $didDispatchForRequest;
        }

        return $didDispatchForRequest;
    }

    private function extractSseData(string $chunk): ?string
    {
        $lines = preg_split('/\r?\n/', $chunk) ?: [];
        $dataLines = [];

        foreach ($lines as $line) {
            if (! str_starts_with($line, 'data:')) {
                continue;
            }

            $dataLines[] = ltrim(substr($line, 5));
        }

        if ($dataLines === []) {
            return null;
        }

        return implode("\n", $dataLines);
    }

    private function decodeAndDispatchSseData(string $requestId, string $data): bool
    {
        try {
            $decoded = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
            if (! is_array($decoded)) {
                return false;
            }

            $this->logger->debug('Received Streamable HTTP event', ['response' => $decoded, 'requestId' => $requestId]);

            return $this->dispatchDecodedPayload($requestId, $decoded);
        } catch (\JsonException $e) {
            $this->logger->error('Failed to decode Streamable HTTP SSE data', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return false;
        }
    }

    private function cleanupResponseStream(string $requestId): void
    {
        unset($this->activeResponseStreams[$requestId], $this->responseBuffers[$requestId]);
    }

    /**
     * Dispatch one decoded payload and return true when it contains a response for the request ID.
     *
     * @param array<mixed> $decoded
     */
    private function dispatchDecodedPayload(string $requestId, array $decoded): bool
    {
        // Support batch responses by processing each entry.
        if ($this->isListArray($decoded)) {
            $didDispatchForRequest = false;

            foreach ($decoded as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $this->dispatchResponse($item);
                $didDispatchForRequest = $didDispatchForRequest || (isset($item['id']) && (string) $item['id'] === $requestId);
            }

            return $didDispatchForRequest;
        }

        $this->dispatchResponse($decoded);

        return isset($decoded['id']) && (string) $decoded['id'] === $requestId;
    }

    /**
     * @param array<mixed> $array
     */
    private function isListArray(array $array): bool
    {
        if ($array === []) {
            return false;
        }

        return array_keys($array) === range(0, count($array) - 1);
    }

    private function normalizeContentType(string $contentType): string
    {
        return strtolower(trim(strtok($contentType, ';') ?: ''));
    }

    /**
     * Update session ID from response if the header is present
     *
     * @param ResponseInterface $response The HTTP response
     */
    private function updateSessionIdFromResponse(ResponseInterface $response): void
    {
        if (! $response->hasHeader('Mcp-Session-Id')) {
            return;
        }

        $this->sessionId = $response->getHeaderLine('Mcp-Session-Id');
        $this->logger->debug('Updated session ID from response', ['sessionId' => $this->sessionId]);
    }
}
