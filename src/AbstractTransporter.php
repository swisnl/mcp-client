<?php

namespace Swis\McpClient;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use function React\Async\await;

use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use Swis\McpClient\Exceptions\ConnectionFailedException;
use Swis\McpClient\Requests\InitializeRequest;
use Swis\McpClient\Requests\RequestInterface;
use Swis\McpClient\Results\ResultFactory;

/**
 * Abstract base transporter implementation with common functionality.
 */
abstract class AbstractTransporter implements TransporterInterface
{
    /**
     * @var bool Whether the transporter is connected
     */
    protected bool $connected = false;

    /**
     * @var EventDispatcherInterface|null The event dispatcher
     */
    protected ?EventDispatcherInterface $eventDispatcher = null;

    /**
     * The requestMap is used to map response request IDs to the corresponding requests
     *
     * @var array<string, RequestInterface> The request map containing request IDs and the corresponding requests
     */
    protected array $requestMap = [];

    /**
     * @var LoopInterface The event loop
     */
    protected LoopInterface $loop;

    /**
     * @var LoggerInterface The logger
     */
    protected LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param LoggerInterface|null $logger Optional logger
     * @param LoopInterface|null $loop Optional event loop
     */
    public function __construct(?LoggerInterface $logger = null, ?LoopInterface $loop = null)
    {
        $this->logger = $logger ?? new NullLogger();
        $this->loop = $loop ?? Loop::get();
    }

    /**
     * {@inheritdoc}
     */
    public function isConnected(): bool
    {
        return $this->connected;
    }

    /**
     * {@inheritdoc}
     */
    public function initializeConnection(EventDispatcherInterface $eventDispatcher, array $capabilities, array $clientInfo, string $protocolVersion): array
    {
        $this->connect();
        $this->listen($eventDispatcher);

        $initRequest = new InitializeRequest(
            capabilities: $capabilities,
            clientInfo: $clientInfo,
            protocolVersion: $protocolVersion
        );

        try {
            $serverInfo = [];

            await(
                $this
                    ->sendRequest($initRequest)
                    ->then(function ($result) use (&$serverInfo) {
                        if ($result instanceof ResponseInterface) {
                            $result = @json_decode((string) $result->getBody(), true) ?: [];
                        }

                        $serverInfo = (array) $result;
                    })
                    ->then(fn () => register_shutdown_function(fn () => $this->loop?->stop()))
                    ->then(fn () => register_shutdown_function([$this, 'disconnect']))
                    ->then(fn () => $this->afterInitialization())
            );

            return $serverInfo;
        } catch (\Throwable $e) {
            throw new ConnectionFailedException('Failed to connect to MCP server', 0, $e);
        }
    }

    abstract public function connect(): void;

    protected function afterInitialization(): void
    {
        // This method can be overridden by subclasses to perform additional actions after initialization.
    }

    /**
     * {@inheritdoc}
     */
    public function listen(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function bindRequest(RequestInterface $request): void
    {
        $this->requestMap[$request->getId()] = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function clearRequestMap(): void
    {
        $this->requestMap = [];
    }

    /**
     * Dispatch a response event
     *
     * @param array $response The response from the server
     */
    protected function dispatchResponse(array $response): void
    {
        if ($this->eventDispatcher === null) {
            $this->logger->warning('Event dispatcher not set, cannot dispatch response');

            return;
        }

        // Create a result object if possible
        $result = ResultFactory::createFromResponse($response, $this->requestMap);

        $event = new ResponseEvent($response, $result);
        $this->eventDispatcher->dispatch($event);
    }
}
