<?php

namespace Swis\McpClient\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Swis\McpClient\Client;

/**
 * Base class for integration tests
 */
abstract class IntegrationTestCase extends TestCase
{
    /**
     * @var Client The client instance
     */
    protected Client $client;

    /**
     * @var resource The process handle for the server
     */
    protected $processHandle;

    /**
     * @var TestLogger The test logger
     */
    protected TestLogger $logger;

    /**
     * Create a test logger that stores logs in memory for inspection
     *
     * @return TestLogger
     */
    protected function createLogger(): TestLogger
    {
        return new TestLogger();
    }

    /**
     * Set up the test environment
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Start the mock server process and create a client
        $this->logger = $this->createLogger();

        // Create a new, isolated loop for each test
        $loop = \React\EventLoop\Factory::create();

        [$this->client, $this->processHandle] = Client::withProcess(
            PHP_BINARY . ' ' . realpath(__DIR__ . '/Mock/server.php'),
            logger: $this->logger,
            loop: $loop
        );

        // Set client info
        $this->client->withClientInfo([
            'name' => 'Test Client',
            'version' => '1.0.0-test',
        ]);

        // Connect to the server
        $this->client->connect();
    }

    /**
     * Clean up resources after the test
     */
    protected function tearDown(): void
    {
        if (isset($this->client)) {
            $this->client->disconnect();
        }

        // Make sure to terminate and close process resources
        if (isset($this->processHandle) && is_resource($this->processHandle)) {
            proc_terminate($this->processHandle);
            proc_close($this->processHandle);
            $this->processHandle = null;
        }

        // Get the loop from the client before unset
        $loop = $this->client->getLoop();

        // Stop the test's isolated loop (this won't affect other tests since each has its own loop)
        if ($loop) {
            $loop->stop();
        }

        // Clear state
        unset($this->client);
        $this->logger->clear();


        parent::tearDown();
    }
}

/**
 * Logger that stores logs in memory for testing
 */
class TestLogger extends AbstractLogger implements LoggerInterface
{
    /**
     * @var array Stored log entries
     */
    private array $logs = [];

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = []): void
    {
        $this->logs[] = [
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ];
    }

    /**
     * Get all logs
     *
     * @return array
     */
    public function getLogs(): array
    {
        return $this->logs;
    }

    /**
     * Clear logs
     */
    public function clear(): void
    {
        $this->logs = [];
    }

    /**
     * Check if any log contains the specified text
     *
     * @param string $text The text to search for
     * @return bool
     */
    public function hasLogContaining(string $text): bool
    {
        foreach ($this->logs as $log) {
            if (strpos($log['message'], $text) !== false) {
                return true;
            }
        }

        return false;
    }
}
