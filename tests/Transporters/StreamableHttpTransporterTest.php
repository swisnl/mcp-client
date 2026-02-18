<?php

namespace Swis\McpClient\Tests\Transporters;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

use function React\Promise\resolve;

use Swis\McpClient\EventDispatcher;
use Swis\McpClient\Requests\RequestInterface;
use Swis\McpClient\Transporters\StreamableHttpTransporter;

class StreamableHttpTransporterTest extends TestCase
{
    public function testInitializeConnectionRegistersEventDispatcherWithoutSseConnect(): void
    {
        $transporter = new class ('https://example.com/mcp') extends StreamableHttpTransporter {
            public bool $connectCalled = false;

            public function connect(): void
            {
                $this->connectCalled = true;
            }

            protected function doSendRequest(RequestInterface $request): PromiseInterface
            {
                return resolve(new Response(
                    200,
                    ['Content-Type' => 'application/json', 'Mcp-Session-Id' => 'session-1'],
                    json_encode([
                        'serverInfo' => ['name' => 'Mock Server', 'version' => '1.0.0'],
                        'protocolVersion' => '2025-03-26',
                        'capabilities' => [],
                    ], JSON_THROW_ON_ERROR)
                ));
            }

            public function eventDispatcherForTest(): ?EventDispatcherInterface
            {
                return $this->eventDispatcher;
            }
        };

        $eventDispatcher = new EventDispatcher();
        $serverInfo = $transporter->initializeConnection($eventDispatcher, [], [], '2025-03-26');

        $this->assertFalse($transporter->connectCalled, 'Streamable HTTP initialize should not call SSE connect().');
        $this->assertSame($eventDispatcher, $transporter->eventDispatcherForTest());
        $this->assertSame('Mock Server', $serverInfo['serverInfo']['name']);
    }
}
