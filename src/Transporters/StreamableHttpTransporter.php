<?php

namespace Swis\McpClient\Transporters;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;

use function React\Async\await;

use React\Promise\PromiseInterface;
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

    public function initializeConnection(EventDispatcherInterface $eventDispatcher, array $capabilities, array $clientInfo, string $protocolVersion): array
    {
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
        return parent::sendRequest($request)
            ->then(function (ResponseInterface $response) {

                match ($response->getHeaderLine('Content-Type')) {
                    'text/event-stream' => $this->handleSseResponse($response),
                    'application/json' => $this->handleJsonResponse($response),
                    default => throw new \RuntimeException('Unexpected Content-Type: ' . $response->getHeaderLine('Content-Type')),
                };

                return $response;
            });
    }

    protected function handleJsonResponse(ResponseInterface $response): void
    {
        $this->buffer .= $response->getBody();
        $this->processBuffer();
    }

    protected function getDefaultHeaders(): array
    {
        $headers = parent::getDefaultHeaders();
        $headers['Accept'] = 'application/json;text/event-stream';

        if (isset($this->sessionId)) {
            $headers['Mcp-Session-Id'] = $this->sessionId;
        }

        return $headers;
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
