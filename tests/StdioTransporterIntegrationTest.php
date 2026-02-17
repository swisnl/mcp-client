<?php

namespace Swis\McpClient\Tests;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Swis\McpClient\Client;
use Swis\McpClient\EventDispatcher;
use Swis\McpClient\Schema\Tool;
use Swis\McpClient\Transporters\SseTransporter;
use Swis\McpClient\Transporters\StdioTransporter;
use Swis\McpClient\Transporters\StreamableHttpTransporter;

/**
 * Integration test for the StdioTransporter
 */
class StdioTransporterIntegrationTest extends TestCase
{
    /**
     * Test creating a client with StdioTransporter
     *
     * This test just verifies that the client creation works and does basic validation
     * of the client configuration. The actual communication flow is tested
     * in a manual test in the examples directory.
     */
    #[RunInSeparateProcess]
    public function testClientCreation(): void
    {
        // Create a logger that stores messages
        $logger = new class () extends AbstractLogger {
            public array $messages = [];

            public function log($level, $message, array $context = []): void
            {
                $this->messages[] = [
                    'level' => $level,
                    'message' => $message,
                    'context' => $context,
                ];
            }
        };

        // Create clients with different transporters
        $stdioClient = Client::withStdio($logger);
        $sseClient = Client::withSse('https://example.com/sse', $logger);
        $sseClientWithHeaders = Client::withSse(
            'https://example.com/sse',
            $logger,
            null,
            ['Authorization' => 'Bearer test-token']
        );
        $streamableClientWithHeaders = Client::withStreamableHttp(
            'https://example.com/mcp',
            $logger,
            null,
            ['Authorization' => 'Bearer test-token']
        );

        // Verify client properties
        $this->assertInstanceOf(Client::class, $stdioClient);
        $this->assertInstanceOf(StdioTransporter::class, $this->getObjectProperty($stdioClient, 'transporter'));
        $this->assertInstanceOf(EventDispatcher::class, $this->getObjectProperty($stdioClient, 'eventDispatcher'));

        $this->assertInstanceOf(Client::class, $sseClient);
        $this->assertInstanceOf(SseTransporter::class, $this->getObjectProperty($sseClient, 'transporter'));
        $this->assertInstanceOf(Client::class, $sseClientWithHeaders);
        $this->assertInstanceOf(SseTransporter::class, $this->getObjectProperty($sseClientWithHeaders, 'transporter'));
        $this->assertInstanceOf(Client::class, $streamableClientWithHeaders);
        $this->assertInstanceOf(StreamableHttpTransporter::class, $this->getObjectProperty($streamableClientWithHeaders, 'transporter'));
        $this->assertSame(
            ['Authorization' => 'Bearer test-token'],
            $this->getObjectProperty($this->getObjectProperty($sseClientWithHeaders, 'transporter'), 'customHeaders')
        );
        $this->assertSame(
            ['Authorization' => 'Bearer test-token'],
            $this->getObjectProperty($this->getObjectProperty($streamableClientWithHeaders, 'transporter'), 'customHeaders')
        );

        // Test client configuration
        $clientInfo = $this->getObjectProperty($stdioClient, 'clientInfo');
        $this->assertEquals('PHP MCP Client', $clientInfo['name']);
        $this->assertEquals('1.0.0', $clientInfo['version']);

        // Test updating client info
        $stdioClient->withClientInfo(['name' => 'Test Client', 'version' => '2.0.0']);
        $clientInfo = $this->getObjectProperty($stdioClient, 'clientInfo');
        $this->assertEquals('Test Client', $clientInfo['name']);
        $this->assertEquals('2.0.0', $clientInfo['version']);

        // Test setting capabilities
        $stdioClient->withCapabilities(['test' => true]);
        $capabilities = $this->getObjectProperty($stdioClient, 'capabilities');
        $this->assertEquals(['test' => true], $capabilities);

        // Test creating a tool object from schema
        $tool = new Tool(
            'test.tool',
            ['param1' => ['type' => 'string', 'description' => 'Test parameter']],
            'Test tool'
        );

        $this->assertEquals('test.tool', $tool->getName());
        $this->assertEquals('Test tool', $tool->getDescription());

        // Use Tool::fromArray to test conversion from API data
        $toolFromArray = Tool::fromArray([
            'name' => 'array.tool',
            'description' => 'Tool from array',
            'inputSchema' => ['foo' => ['type' => 'string']],
        ]);

        $this->assertEquals('array.tool', $toolFromArray->getName());
        $this->assertEquals('Tool from array', $toolFromArray->getDescription());
    }

    /**
     * Test protocol version setting
     */
    #[RunInSeparateProcess]
    public function testProtocolVersion(): void
    {
        $client = Client::withStdio();

        // Default protocol version
        $this->assertEquals('2025-03-26', $this->getObjectProperty($client, 'protocolVersion'));

        // Update protocol version
        $client->withProtocolVersion('2024-11-05');
        $this->assertEquals('2024-11-05', $this->getObjectProperty($client, 'protocolVersion'));
    }

    /**
     * Helper method to access private/protected properties
     */
    private function getObjectProperty(object $object, string $propertyName)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
