<?php

namespace Swis\McpClient\Transporters;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

use function React\Async\await;

use React\EventLoop\LoopInterface;
use React\Http\Browser;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

use function React\Promise\reject;

use React\Stream\ReadableStreamInterface;
use Swis\McpClient\AbstractTransporter;
use Swis\McpClient\Exceptions\NotConnectedException;
use Swis\McpClient\Exceptions\SseConnectionException;
use Swis\McpClient\Requests\RequestInterface;

/**
 * Transporter that communicates with the MCP server via Server-Sent Events (SSE).
 *
 * This transporter is non-blocking, allowing for simultaneous reading and writing.
 * Uses ReactPHP for HTTP requests and event handling.
 */
class SseTransporter extends AbstractTransporter
{
    /**
     * @var string The initial SSE endpoint URL
     */
    private string $initialEndpoint;

    /**
     * @var string|null The endpoint URL for sending requests (may be updated from SSE)
     */
    private ?string $requestEndpoint = null;

    /**
     * @var Browser The ReactPHP HTTP browser for requests
     */
    private Browser $browser;

    /**
     * @var ReadableStreamInterface|null The SSE stream
     */
    private ?ReadableStreamInterface $sseStream = null;

    /**
     * @var string Buffer for SSE data
     */
    private string $buffer = '';

    /**
     * @var Deferred<mixed>|null Deferred for tracking SSE connection status
     */
    private ?Deferred $connectionDeferred = null;

    /**
     * Constructor
     *
     * @param string $endpoint The SSE endpoint URL
     * @param LoggerInterface|null $logger Optional logger
     * @param LoopInterface|null $loop Optional event loop
     */
    public function __construct(
        string $endpoint,
        ?LoggerInterface $logger = null,
        ?LoopInterface $loop = null
    ) {
        parent::__construct($logger, $loop);
        $this->initialEndpoint = $endpoint;
        $this->browser = new Browser($this->loop);
    }

    /**
     * {@inheritdoc}
     */
    public function connect(): void
    {
        if ($this->connected) {
            return;
        }

        // Create a deferred to track connection status
        $this->connectionDeferred = new Deferred();

        // Initialize the SSE connection
        $this->initSseConnection();

        // Block until the connection is established or times out
        try {
            await($this->connectionDeferred->promise());
            $this->connected = true;
            $this->logger->debug('Connected to MCP server via SSE', [
                'initialEndpoint' => $this->initialEndpoint,
                'requestEndpoint' => $this->requestEndpoint,
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to connect to MCP server', ['error' => $e->getMessage()]);

            throw new SseConnectionException('Failed to connect to MCP server: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect(): void
    {
        if (! $this->connected) {
            return;
        }

        if ($this->sseStream instanceof ReadableStreamInterface) {
            $this->sseStream->close();
            $this->sseStream = null;
        }

        $this->connected = false;
        $this->requestEndpoint = null;
        $this->logger->debug('Disconnected from MCP server');
    }

    /**
     * {@inheritdoc}
     */
    public function sendRequest(RequestInterface $request): PromiseInterface
    {
        if (! $this->connected) {
            return reject(new NotConnectedException('Not connected to MCP server'));
        }

        if ($this->requestEndpoint === null) {
            return reject(new SseConnectionException('Request endpoint not available, SSE connection not fully established'));
        }

        /** @var string $requestEndpoint */
        $requestEndpoint = $this->requestEndpoint;

        $requestData = json_encode($request, JSON_UNESCAPED_SLASHES);
        assert($requestData !== false);

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $this->logger->debug('Sending request to MCP server', [
            'endpoint' => $requestEndpoint,
            'request' => $request,
        ]);

        $this->bindRequest($request);

        return $this->browser->post($requestEndpoint, $headers, $requestData)
            ->then(
                function ($response) {
                    return (string) $response->getBody();
                },
                function (\Exception $e) {
                    $this->logger->error('Request failed', [
                        'error' => $e->getMessage(),
                    ]);

                    throw $e;
                }
            );
    }

    /**
     * {@inheritdoc}
     */
    public function listen(EventDispatcherInterface $eventDispatcher): void
    {
        parent::listen($eventDispatcher);

        if (! $this->connected) {
            $this->connect();
        }
    }

    /**
     * Initialize the SSE connection
     */
    protected function initSseConnection(): void
    {
        $headers = [
            'Accept' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
        ];

        $this->logger->debug('Initializing SSE connection', ['endpoint' => $this->initialEndpoint]);

        // Use ReactPHP's streaming capabilities to handle the SSE stream
        $this->browser->requestStreaming('GET', $this->initialEndpoint, $headers)
            ->then(
                function (ResponseInterface $response) {
                    $stream = $response->getBody();

                    if (! $stream instanceof ReadableStreamInterface) {
                        $error = new SseConnectionException('Invalid stream returned from SSE request');
                        $this->connectionDeferred?->reject($error);

                        return;
                    }

                    $this->sseStream = $stream;
                    $this->logger->debug('SSE stream established');

                    $stream->on('data', function ($chunk) {
                        $this->buffer .= $chunk;
                        $this->processBuffer();
                    });

                    $stream->on('error', function (\Exception $e) {
                        $this->logger->error('SSE stream error', [
                            'error' => $e->getMessage(),
                        ]);

                        $this->connectionDeferred?->reject($e);
                        $this->reconnect();
                    });

                    $stream->on('close', function () {
                        $this->logger->warning('SSE stream closed unexpectedly');
                    });
                },
                function (\Exception $e) {
                    $this->logger->error('Failed to establish SSE connection', [
                        'error' => $e->getMessage(),
                    ]);

                    $this->connectionDeferred?->reject($e);
                }
            );
    }

    /**
     * Process the buffer for SSE events
     */
    protected function processBuffer(): void
    {
        while (($pos = strpos($this->buffer, "\n\n")) === false) {
            return; // Not enough data in buffer yet
        }

        // Extract the next event chunk
        $chunk = substr($this->buffer, 0, $pos);
        $this->buffer = substr($this->buffer, $pos + 2);

        // Handle event with type (event: field is present)
        if (preg_match('/^event: ([^\n]+)\ndata: (.+)$/m', $chunk, $matches)) {
            $this->handleTypedEvent($matches[1], $matches[2]);
            $this->processBuffer(); // Process any remaining events in buffer

            return;
        }

        // Handle data-only event (no event: field)
        if (preg_match('/^data: (.+)$/m', $chunk, $matches)) {
            $this->handleDataEvent($matches[1]);
            $this->processBuffer(); // Process any remaining events in buffer

            return;
        }
    }

    /**
     * Handle a typed event (with event: field)
     */
    protected function handleTypedEvent(string $eventType, string $data): void
    {
        $this->logger->debug('Received SSE event', ['type' => $eventType]);

        if ($eventType === 'endpoint') {
            $this->requestEndpoint = $data;
            $this->logger->debug('Received endpoint URL from SSE', ['endpoint' => $this->requestEndpoint]);

            if ($this->connectionDeferred) {
                $this->connectionDeferred->resolve(true);
            }

            return;
        }

        $this->decodeAndDispatchData($data, 'event');
    }

    /**
     * Handle a data-only event
     */
    protected function handleDataEvent(string $data): void
    {
        $this->decodeAndDispatchData($data, 'data');
    }

    /**
     * Decode JSON data and dispatch the response
     */
    protected function decodeAndDispatchData(string $data, string $context): void
    {
        try {
            /** @var array $response */
            $response = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
            $this->logger->debug("Received SSE $context", ['response' => $response]);
            $this->dispatchResponse($response);
        } catch (\JsonException $e) {
            $this->logger->error('Failed to decode SSE data', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
        }
    }

    /**
     * Attempt to reconnect when the connection is lost
     */
    protected function reconnect(): void
    {
        if ($this->sseStream !== null) {
            $this->sseStream->close();
            $this->sseStream = null;
        }

        $this->connected = false;
        $this->connectionDeferred = new Deferred();

        $this->logger->debug('Attempting to reconnect to MCP server...');

        // Wait a moment before reconnecting
        $this->loop->addTimer(1.0, function () {
            $this->initSseConnection();

            try {
                assert($this->connectionDeferred instanceof Deferred);
                await($this->connectionDeferred->promise());
                $this->connected = true;
                $this->logger->debug('Reconnected to MCP server via SSE');
            } catch (\Exception $e) {
                $this->logger->error('Failed to reconnect to MCP server', ['error' => $e->getMessage()]);
            }
        });
    }
}
