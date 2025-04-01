<?php

namespace Swis\McpClient\Transporters;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;

use function React\Promise\reject;
use function React\Promise\resolve;

use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;
use Swis\McpClient\AbstractTransporter;
use Swis\McpClient\Factories\ProcessFactory;
use Swis\McpClient\Requests\RequestInterface;

/**
 * Transporter that communicates with the MCP server via I/O streams.
 *
 * This transporter is non-blocking, allowing for simultaneous reading and writing.
 * It requires valid input/output streams to be provided, typically for communicating
 * with an external process. Use the static forProcess() method for the most common
 * use case of starting and communicating with a process.
 */
class StdioTransporter extends AbstractTransporter
{
    /**
     * @var resource|string|null Input stream resource
     */
    private $inputResource = null;

    /**
     * @var resource|string|null Output stream resource
     */
    private $outputResource = null;

    /**
     * @var resource|string|null Error stream resource
     */
    private $errorResource = null;

    /**
     * @var bool Whether to close streams on disconnect
     */
    private bool $shouldCloseStreams = true;

    /**
     * @var ReadableResourceStream|null Input stream
     */
    private ?ReadableResourceStream $readableStream = null;

    /**
     * @var WritableResourceStream|null Output stream
     */
    private ?WritableResourceStream $writableStream = null;

    /**
     * @var ReadableResourceStream|null Error stream
     */
    private ?ReadableResourceStream $errorStream = null;

    /**
     * @var string Buffer for incoming data
     */
    private string $buffer = '';

    /**
     * @var callable[] Array of callbacks to be called when streams close and need to be reconnected
     */
    private array $reconnectCallbacks = [];

    /**
     * @var array<string> Error bag for registering errors from the error stream
     */
    private array $errorBag = [];

    /**
     * Constructor
     *
     * @param resource|string|null $inputStream Input stream resource or string identifier (defaults to null)
     * @param resource|string|null $outputStream Output stream resource or string identifier (defaults to null)
     * @param bool $shouldCloseStreams Whether to close the streams on disconnect
     * @param LoggerInterface|null $logger Optional logger
     * @param LoopInterface|null $loop Optional event loop
     * @param resource|string|null $errorStream Optional error stream resource for catching possible errors before the connection gets closed
     */
    public function __construct(
        $inputStream = null,
        $outputStream = null,
        bool $shouldCloseStreams = true,
        ?LoggerInterface $logger = null,
        ?LoopInterface $loop = null,
        $errorStream = null,
    ) {
        parent::__construct($logger, $loop);

        $this->inputResource = $inputStream;
        $this->outputResource = $outputStream;
        $this->errorResource = $errorStream;
        $this->shouldCloseStreams = $shouldCloseStreams;
    }

    /**
     * {@inheritdoc}
     */
    public function connect(): void
    {
        if ($this->connected) {
            return;
        }

        // Handle input stream - no default streams now since this should connect to another process
        if (is_string($this->inputResource)) {
            $this->inputResource = fopen($this->inputResource, 'r');
        }

        // Handle output stream
        if (is_string($this->outputResource)) {
            $this->outputResource = fopen($this->outputResource, 'w');
        }

        // If no resources are provided, throw an exception - we need both to communicate
        if (! is_resource($this->inputResource) || ! is_resource($this->outputResource)) {
            throw new \Swis\McpClient\Exceptions\InvalidStreamsException('No valid input/output streams provided. Use StdioTransporter::forProcess() or provide valid stream resources.');
        }

        // Set to non-blocking mode
        stream_set_blocking($this->inputResource, false);
        stream_set_blocking($this->outputResource, false);

        // Create ReactPHP streams
        $this->readableStream = new ReadableResourceStream($this->inputResource, $this->loop);
        $this->writableStream = new WritableResourceStream($this->outputResource, $this->loop);

        $this->setupListeners();
        $this->setupErrorListening();

        $this->connected = true;
        $this->logger->debug('Connected to MCP server via I/O streams');
    }

    /**
     * Set custom input and output streams
     *
     * @param resource|string|null $inputStream Input stream resource or string identifier
     * @param resource|string|null $outputStream Output stream resource or string identifier
     * @param bool $shouldCloseStreams Whether to close the streams on disconnect
     * @return void
     */
    public function setStreams($inputStream, $outputStream, bool $shouldCloseStreams = true): void
    {
        if ($this->connected) {
            $this->disconnect();
        }

        $this->inputResource = $inputStream;
        $this->outputResource = $outputStream;
        $this->shouldCloseStreams = $shouldCloseStreams;
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect(): void
    {
        if (! $this->connected) {
            return;
        }

        $this->connected = false;

        // Close ReactPHP streams
        $this->readableStream?->close();
        $this->writableStream?->close();
        $this->errorStream?->close();

        $this->readableStream = null;
        $this->writableStream = null;
        $this->errorStream = null;

        // Close resource streams if configured to do so
        if ($this->shouldCloseStreams) {
            if (is_resource($this->inputResource)) {
                fclose($this->inputResource);
            }

            if (is_resource($this->outputResource)) {
                fclose($this->outputResource);
            }

            if (is_resource($this->errorResource)) {
                fclose($this->errorResource);
            }
        }

        $this->logger->debug('Disconnected from MCP server');
    }

    /**
     * Send a request to the MCP server
     *
     * @param RequestInterface $request The request to send
     * @return \React\Promise\PromiseInterface<mixed> A promise that resolves when the request has been sent
     */
    public function sendRequest(RequestInterface $request): PromiseInterface
    {
        if (! $this->connected) {
            return reject(new \Swis\McpClient\Exceptions\NotConnectedException('Not connected to MCP server'));
        }

        $json = json_encode($request) . "\n";
        $this->bindRequest($request);

        try {
            assert($this->writableStream instanceof WritableResourceStream);
            $this->writableStream->write($json);
            $this->logger->debug('Sent request to MCP server', ['request' => $request]);

            return resolve(['id' => $request->getId()]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to send request', [
                'error' => $e->getMessage(),
                'request' => $request,
            ]);

            return reject($e);
        }
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
     * Get the error bag
     *
     * @return array<string> The error bag
     */
    public function getErrorBag(): array
    {
        return $this->errorBag;
    }

    /**
     * Setup event listeners for the input/output streams
     */
    private function setupListeners(): void
    {
        assert($this->readableStream instanceof ReadableResourceStream);
        /** @var ReadableResourceStream $readableStream */
        $readableStream = $this->readableStream;

        $readableStream->on('data', function (string $data) {
            $this->buffer .= $data;
            $this->processBuffer();
        });

        $readableStream->on('error', function (\Throwable $e) {
            $this->logger->error('Error in input stream: ' . $e->getMessage());
        });

        $readableStream->on('close', function () {
            $this->logger->debug('Input stream closed');
            // Trigger reconnect callbacks to attempt auto-healing
            $this->triggerReconnectAttempt();
        });

        assert($this->writableStream instanceof WritableResourceStream);
        /** @var WritableResourceStream $writableStream */
        $writableStream = $this->writableStream;

        // Also listen for write stream close events
        $writableStream->on('close', function () {
            $this->logger->debug('Output stream closed');
            // Trigger reconnect callbacks to attempt auto-healing
            $this->triggerReconnectAttempt();
        });

        $writableStream->on('error', function (\Throwable $e) {
            $this->logger->error('Error in output stream: ' . $e->getMessage());
        });
    }

    /**
     * Setup event listeners for the error stream
     */
    private function setupErrorListening(): void
    {
        if (is_string($this->errorResource)) {
            $this->errorResource = fopen($this->errorResource, 'r');
        }

        if (! is_resource($this->errorResource)) {
            return;
        }

        stream_set_blocking($this->errorResource, false);

        // Create ReactPHP error stream
        $this->errorStream = new ReadableResourceStream($this->errorResource, $this->loop);

        $this->errorStream->on('data', function (string $data) {
            $this->errorBag[] = $data;

            $this->logger->warning('Process stderr output', [
                'stderr' => $data,
            ]);
        });

        $this->errorStream->on('error', function (\Throwable $e) {
            $this->errorBag[] = $e->getMessage();

            $this->logger->error('Process stderr error', [
                'error' => $e->getMessage(),
            ]);
        });

    }

    /**
     * Register a callback function to be called when streams need reconnection
     *
     * @param callable $callback Function that should return array with input and output streams
     * @return void
     */
    public function onReconnectAttempt(callable $callback): void
    {
        $this->reconnectCallbacks[] = $callback;
    }

    /**
     * Trigger all registered reconnect callbacks to attempt to heal the connection
     *
     * @return bool Whether reconnection was successful
     */
    protected function triggerReconnectAttempt(): bool
    {
        // Only attempt reconnecting if we should have been connected on the first place
        if (! $this->connected) {
            return false;
        }

        if (empty($this->reconnectCallbacks)) {
            $this->logger->debug('No reconnect callbacks registered, cannot auto-heal');

            return false;
        }

        $this->connected = false;

        foreach ($this->reconnectCallbacks as $callback) {
            try {
                // Call the callback which should provide new streams
                $streams = $callback();

                if ($streams === null) {
                    $this->logger->debug('Reconnect callback returned null, not attempting auto-heal');

                    continue;
                }

                if (! is_array($streams) || count($streams) < 2) {
                    $this->logger->debug('Invalid reconnect callback result, expected array with input and output streams');

                    continue;
                }

                [$inputStream, $outputStream] = $streams;

                assert(is_resource($inputStream) || is_string($inputStream));
                assert(is_resource($outputStream) || is_string($outputStream));

                // Set the new streams and reconnect
                $this->setStreams($inputStream, $outputStream, $this->shouldCloseStreams);

                if (count($streams) === 3) {
                    $errorStream = $streams[2];
                    assert(is_resource($errorStream) || is_string($errorStream));
                    $this->errorResource = $errorStream;
                    $this->setupErrorListening();
                }

                $this->connect();

                $this->logger->debug('Successfully auto-healed connection with new streams');

                return true;
            } catch (\Throwable $e) {
                $this->logger->error('Failed to auto-heal connection: ' . $e->getMessage());
            }
        }

        return false;
    }

    /**
     * Process the buffer for complete JSON messages
     */
    protected function processBuffer(): void
    {
        // Process each line as a separate JSON message
        while (($pos = strpos($this->buffer, "\n")) !== false) {
            $line = substr($this->buffer, 0, $pos);
            $this->buffer = substr($this->buffer, $pos + 1);

            if (empty(trim($line))) {
                continue;
            }

            try {
                /** @var array $response */
                $response = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
                $this->logger->debug('Received response from MCP server', ['response' => $response]);
                $this->dispatchResponse($response);
            } catch (\JsonException $e) {
                $this->logger->error('Failed to decode response', [
                    'error' => $e->getMessage(),
                    'line' => $line,
                ]);
            }
        }
    }

    /**
     * Create a transporter connected to a process
     *
     * @param string $command The command to execute
     * @param array<string, scalar> $env Additional environment variables for the process
     * @param string|null $cwd Working directory for the process
     * @param LoggerInterface|null $logger Optional logger
     * @param int $autoRestartAmount Whether to automatically restart the process if it terminates
     * @param LoopInterface|null $loop Optional event loop
     * @return array{0: StdioTransporter, 1: resource} Transporter and process resource
     * @throws \Swis\McpClient\Exceptions\ProcessStartException If the process could not be started
     * @deprecated Use \Swis\McpClient\Factory\ProcessFactory::createTransporterForProcess() instead
     */
    public static function forProcess(
        string $command,
        array $env = [],
        ?string $cwd = null,
        ?LoggerInterface $logger = null,
        int $autoRestartAmount = 0,
        ?LoopInterface $loop = null
    ): array {
        return ProcessFactory::createTransporterForProcess(
            $command,
            $env,
            $cwd,
            $logger,
            $autoRestartAmount,
            $loop
        );
    }
}
