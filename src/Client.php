<?php

namespace Swis\McpClient;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use Swis\McpClient\Factories\ProcessFactory;
use Swis\McpClient\Requests\RequestInterface;
use Swis\McpClient\Transporters\SseTransporter;
use Swis\McpClient\Transporters\StdioTransporter;
use Swis\McpClient\Transporters\StreamableHttpTransporter;

/**
 * MCP client that manages communication with MCP servers.
 */
class Client
{
    use ClientRequestTrait;

    /**
     * @var array<string, callable> Response callbacks indexed by request ID
     */
    protected array $responseCallbacks = [];

    /**
     * @var array<string, RequestInterface> Sent requests indexed by request ID
     */
    protected array $sentRequests = [];

    /**
     * @var array<string, Deferred> Pending requests indexed by request ID
     */
    protected array $pending = [];

    /**
     * @var array<string, PromiseInterface> In-flight transport promises indexed by request ID
     */
    protected array $inFlight = [];

    /**
     * @var array<string, bool|float|int|string> Client capabilities
     */
    protected array $capabilities = [];

    /**
     * @var array<string, string> Client information
     */
    protected array $clientInfo = [];

    /**
     * @var string Protocol version
     */
    protected string $protocolVersion = '2025-03-26';

    /**
     * Constructor
     *
     * @param TransporterInterface $transporter The transporter to use
     * @param EventDispatcher $eventDispatcher The event dispatcher
     * @param LoggerInterface|null $logger Optional logger
     * @param LoopInterface|null $loop Optional event loop
     */
    public function __construct(
        protected TransporterInterface $transporter,
        protected EventDispatcher $eventDispatcher,
        protected ?LoggerInterface $logger = null,
        protected ?LoopInterface $loop = null
    ) {
        $this->logger = $logger ?? new NullLogger();
        $this->loop = $loop ?? Loop::get();

        // Set default client info
        $this->clientInfo = [
            'name' => 'PHP MCP Client',
            'version' => '1.0.0',
        ];

        // Register event listener for responses
        $this->eventDispatcher->addListener(ResponseEvent::class, [$this, 'handleResponse']);
    }

    public function eventDispatcher(): EventDispatcher
    {
        return $this->eventDispatcher;
    }

    /**
     * Get the event loop instance
     *
     * @return LoopInterface|null
     */
    public function getLoop(): ?LoopInterface
    {
        return $this->loop;
    }

    /**
     * Set client capabilities
     *
     * @param array<string, string> $capabilities The client capabilities
     * @return self
     */
    public function withCapabilities(array $capabilities): self
    {
        $this->capabilities = array_merge($this->capabilities, $capabilities);

        return $this;
    }

    /**
     * Set client information
     *
     * @param array<string, string> $clientInfo The client information
     * @return self
     */
    public function withClientInfo(array $clientInfo): self
    {
        $this->clientInfo = array_merge($this->clientInfo, $clientInfo);

        return $this;
    }

    /**
     * Set protocol version
     *
     * @param string $protocolVersion The protocol version
     * @return self
     */
    public function withProtocolVersion(string $protocolVersion): self
    {
        $this->protocolVersion = $protocolVersion;

        return $this;
    }

    /**
     * Connect to the MCP server and send initialize request
     * This method will internally await the connection and initialization,
     * blocking until the connection is fully established
     *
     * @param callable|null $initCallback Optional callback for the initialize response
     * @throws \RuntimeException If connection fails
     */
    public function connect(?callable $initCallback = null): void
    {
        $serverInfo = $this->transporter->initializeConnection(
            eventDispatcher: $this->eventDispatcher,
            capabilities: $this->capabilities,
            clientInfo: $this->clientInfo,
            protocolVersion: $this->protocolVersion
        );

        if ($initCallback !== null) {
            $initCallback($serverInfo);
        }
    }

    /**
     * Check if the transporter is connected
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->transporter->isConnected();
    }

    /**
     * Disconnect from the MCP server
     */
    public function disconnect(): void
    {
        foreach ($this->pending as $requestId => $deferred) {
            $deferred->reject(new \RuntimeException("Connection closed before response was received for request ID [$requestId]."));
        }

        $this->transporter->disconnect();

        $this->responseCallbacks = [];
        $this->pending = [];
        $this->inFlight = [];
        $this->sentRequests = [];
        $this->transporter->clearRequestMap();

        // Reset event dispatcher
        $this->eventDispatcher->removeAllListeners();
    }

    /**
     * Send a request to the MCP server
     *
     * @param RequestInterface $request The request to send
     * @param callable|null $callback Optional callback for the response
     * @return PromiseInterface<scalar|array|\Exception> A promise that resolves when the request has been sent
     */
    public function sendRequest(RequestInterface $request, ?callable $callback = null): PromiseInterface
    {
        $requestId = (string) $request->getId();

        // Register the response handler if provided
        if ($callback !== null) {
            $this->responseCallbacks[$requestId] = $callback;
        }

        // Store the request for later result mapping
        $this->sentRequests[$requestId] = $request;
        $this->inFlight[$requestId] = $this->transporter->sendRequest($request);

        return $this->inFlight[$requestId]->then(
            function ($result) use ($requestId) {
                if (! isset($this->pending[$requestId]) && ! isset($this->responseCallbacks[$requestId])) {
                    $this->cleanupRequestState($requestId);
                }

                return $result;
            },
            function (\Throwable $e) use ($requestId) {
                $this->rejectPendingRequest($requestId, $e);

                throw $e;
            }
        );
    }

    /**
     * Send a request to the MCP server and return a promise.
     * The promise will resolve when the server sends a response.
     *
     * @param RequestInterface $request The request to send
     * @return PromiseInterface<mixed> A promise that resolves when the server sends a response
     */
    public function sendRequestAsync(RequestInterface $request): PromiseInterface
    {
        $requestId = (string) $request->getId();

        if (isset($this->pending[$requestId]) || isset($this->inFlight[$requestId])) {
            $deferred = new Deferred();
            $deferred->reject(new \RuntimeException("Request with ID [$requestId] is already in progress."));

            return $deferred->promise();
        }

        $deferred = new Deferred();
        $this->pending[$requestId] = $deferred;

        // Rejection is bridged into $this->pending[$requestId] in sendRequest().
        // Attach a noop rejection handler to avoid unhandled rejection warnings for this internal promise chain.
        $this->sendRequest($request)->then(null, static function (): void {
        });

        return $deferred->promise();
    }

    /**
     * Handle a response event
     *
     * @param ResponseEvent $event The response event
     */
    public function handleResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        // Check if the response has an ID and if we have a handler for it
        if (! isset($response['id'])) {
            $this->logger?->debug('Received response with no ID', ['response' => $response]);

            return;
        }

        $requestId = (string) $response['id'];

        $hasPendingRequest = isset($this->pending[$requestId]);
        $hasCallback = isset($this->responseCallbacks[$requestId]);

        if (! $hasPendingRequest && ! $hasCallback) {
            $this->logger?->debug('Received response with no pending request', ['response' => $response]);

            return;
        }

        // Use the result object if available, otherwise use the raw result data
        $result = $event->hasResult()
            ? $event->getResult()
            : ($response['result'] ?? null);

        if ($hasPendingRequest) {
            $this->pending[$requestId]->resolve($result);
        }

        if ($hasCallback) {
            $this->responseCallbacks[$requestId]($result);
        }

        $this->cleanupRequestState($requestId);
    }

    private function rejectPendingRequest(string $requestId, \Throwable $exception): void
    {
        if (isset($this->pending[$requestId])) {
            $this->pending[$requestId]->reject($exception);
        }

        $this->cleanupRequestState($requestId);
    }

    private function cleanupRequestState(string $requestId): void
    {
        unset(
            $this->pending[$requestId],
            $this->inFlight[$requestId],
            $this->responseCallbacks[$requestId],
            $this->sentRequests[$requestId]
        );
    }

    /**
     * Create a client with SSE transporter
     *
     * @param string $endpoint The SSE endpoint URL
     * @param LoggerInterface|null $logger Optional logger
     * @param LoopInterface|null $loop Optional event loop
     * @param array<string, string> $headers Custom headers to send with every request
     * @return self
     */
    public static function withSse(
        string $endpoint,
        ?LoggerInterface $logger = null,
        ?LoopInterface $loop = null,
        array $headers = []
    ): self {
        $transporter = new SseTransporter($endpoint, $logger, $loop, $headers);
        $eventDispatcher = new EventDispatcher();

        return new self($transporter, $eventDispatcher, $logger, $loop);
    }

    /**
     * Create a client with StreamableHttp transporter
     *
     * @param string $endpoint The SSE endpoint URL
     * @param LoggerInterface|null $logger Optional logger
     * @param LoopInterface|null $loop Optional event loop
     * @param array<string, string> $headers Custom headers to send with every request
     * @return self
     */
    public static function withStreamableHttp(
        string $endpoint,
        ?LoggerInterface $logger = null,
        ?LoopInterface $loop = null,
        array $headers = []
    ): self {
        $transporter = new StreamableHttpTransporter($endpoint, $logger, $loop, $headers);
        $eventDispatcher = new EventDispatcher();

        return new self($transporter, $eventDispatcher, $logger, $loop);
    }

    /**
     * Create a client with stdio transporter connected to stdin/stdout
     *
     * This is rarely useful as a client connecting to itself, but provided for completeness.
     * For most use cases, use withProcess() instead to connect to an external process.
     *
     * @param LoggerInterface|null $logger Optional logger
     * @param LoopInterface|null $loop Optional event loop
     * @return self
     */
    public static function withStdio(
        ?LoggerInterface $logger = null,
        ?LoopInterface $loop = null
    ): self {
        $transporter = new StdioTransporter(
            fopen('php://stdin', 'r'),
            fopen('php://stdout', 'w'),
            false,  // Don't close the standard streams on disconnect
            $logger,
            $loop
        );
        $eventDispatcher = new EventDispatcher();

        return new self($transporter, $eventDispatcher, $logger, $loop);
    }

    /**
     * Create a client with a process transporter
     *
     * @param string $command The command to execute
     * @param array<string, scalar> $env Additional environment variables for the process
     * @param string|null $cwd Working directory for the process
     * @param LoggerInterface|null $logger Optional logger
     * @param int $autoRestartAmount Whether to automatically restart the process if it terminates
     * @param LoopInterface|null $loop Optional event loop
     * @return array{0: self, 1: resource} Client and process resource
     * @throws \RuntimeException If the process could not be started
     */
    public static function withProcess(
        string $command,
        array $env = [],
        ?string $cwd = null,
        ?LoggerInterface $logger = null,
        int $autoRestartAmount = 0,
        ?LoopInterface $loop = null
    ): array {
        [$transporter, $process] = ProcessFactory::createTransporterForProcess(
            command: $command,
            env: $env,
            cwd: $cwd,
            logger: $logger,
            autoRestartAmount: $autoRestartAmount,
            loop: $loop
        );

        $eventDispatcher = new EventDispatcher();
        $client = new self($transporter, $eventDispatcher, $logger, $loop);

        return [$client, $process];
    }
}
