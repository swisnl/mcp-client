<?php

namespace Swis\McpClient\Tests\Transporters;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Loop;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

use function React\Async\await;
use function React\Promise\resolve;

use Swis\McpClient\EventDispatcher;
use Swis\McpClient\Requests\InitializeRequest;
use Swis\McpClient\Requests\PingRequest;
use Swis\McpClient\Requests\RequestInterface;
use Swis\McpClient\ResponseEvent;
use React\Stream\ThroughStream;
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

    public function testSendRequestWithApplicationJsonStringBodyResolvesAndDispatches(): void
    {
        $requestId = 'req-string-' . bin2hex(random_bytes(4));
        $jsonPayload = json_encode([
            'id' => $requestId,
            'jsonrpc' => '2.0',
            'result' => ['pong' => true],
        ], JSON_THROW_ON_ERROR);

        $transporter = new class ('https://example.com/mcp', $requestId, $jsonPayload) extends StreamableHttpTransporter {
            private string $requestId;
            private string $jsonPayload;

            public function __construct(string $endpoint, string $requestId, string $jsonPayload)
            {
                parent::__construct($endpoint);
                $this->requestId = $requestId;
                $this->jsonPayload = $jsonPayload;
            }

            protected function doSendRequest(RequestInterface $request): PromiseInterface
            {
                if ($request instanceof InitializeRequest) {
                    return resolve(new Response(
                        200,
                        ['Content-Type' => 'application/json', 'Mcp-Session-Id' => 'session-1'],
                        json_encode([
                            'serverInfo' => ['name' => 'Mock', 'version' => '1.0'],
                            'protocolVersion' => '2025-03-26',
                            'capabilities' => [],
                        ], JSON_THROW_ON_ERROR)
                    ));
                }

                return resolve(new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    $this->jsonPayload
                ));
            }

            public function eventDispatcherForTest(): ?EventDispatcherInterface
            {
                return $this->eventDispatcher;
            }
        };

        $eventDispatcher = new EventDispatcher();
        $dispatched = null;
        $eventDispatcher->addListener(ResponseEvent::class, function (ResponseEvent $event) use (&$dispatched): void {
            $dispatched = $event->getResponse();
        });

        $transporter->initializeConnection($eventDispatcher, [], [], '2025-03-26');

        $request = (new PingRequest())->withId($requestId);
        $response = await($transporter->sendRequest($request));

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertNotNull($dispatched);
        $this->assertSame($requestId, $dispatched['id'] ?? null);
        $this->assertSame(['pong' => true], $dispatched['result'] ?? null);
    }

    public function testSendRequestWithApplicationJsonStreamBodyBuffersAndDispatches(): void
    {
        $requestId = 'req-stream-' . bin2hex(random_bytes(4));
        $stream = new ThroughStream();
        $jsonPayload = json_encode([
            'id' => $requestId,
            'jsonrpc' => '2.0',
            'result' => ['pong' => true],
        ], JSON_THROW_ON_ERROR);

        $responseWithStream = $this->createMock(ResponseInterface::class);
        $responseWithStream->method('getBody')->willReturn($stream);
        $responseWithStream->method('getHeaderLine')->with('Content-Type')->willReturn('application/json');

        $transporter = new class ('https://example.com/mcp', $responseWithStream) extends StreamableHttpTransporter {
            private ResponseInterface $nextResponse;

            public function __construct(string $endpoint, ResponseInterface $nextResponse)
            {
                parent::__construct($endpoint);
                $this->nextResponse = $nextResponse;
            }

            protected function doSendRequest(RequestInterface $request): PromiseInterface
            {
                if ($request instanceof InitializeRequest) {
                    return resolve(new Response(
                        200,
                        ['Content-Type' => 'application/json', 'Mcp-Session-Id' => 'session-1'],
                        json_encode([
                            'serverInfo' => ['name' => 'Mock', 'version' => '1.0'],
                            'protocolVersion' => '2025-03-26',
                            'capabilities' => [],
                        ], JSON_THROW_ON_ERROR)
                    ));
                }

                return resolve($this->nextResponse);
            }

            public function eventDispatcherForTest(): ?EventDispatcherInterface
            {
                return $this->eventDispatcher;
            }
        };

        $eventDispatcher = new EventDispatcher();
        $dispatched = null;
        $eventDispatcher->addListener(ResponseEvent::class, function (ResponseEvent $event) use (&$dispatched): void {
            $dispatched = $event->getResponse();
        });

        $transporter->initializeConnection($eventDispatcher, [], [], '2025-03-26');

        $request = (new PingRequest())->withId($requestId);
        $promise = $transporter->sendRequest($request);

        Loop::get()->addTimer(0, function () use ($stream, $jsonPayload): void {
            $stream->write($jsonPayload);
            $stream->end();
        });

        $response = await($promise);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertNotNull($dispatched);
        $this->assertSame($requestId, $dispatched['id'] ?? null);
        $this->assertSame(['pong' => true], $dispatched['result'] ?? null);
    }

    public function testSendRequestWithApplicationJsonEmptyBodyThrows(): void
    {
        $requestId = 'req-empty-' . bin2hex(random_bytes(4));

        $transporter = new class ('https://example.com/mcp', $requestId) extends StreamableHttpTransporter {
            private string $requestId;

            public function __construct(string $endpoint, string $requestId)
            {
                parent::__construct($endpoint);
                $this->requestId = $requestId;
            }

            protected function doSendRequest(RequestInterface $request): PromiseInterface
            {
                if ($request instanceof InitializeRequest) {
                    return resolve(new Response(
                        200,
                        ['Content-Type' => 'application/json', 'Mcp-Session-Id' => 'session-1'],
                        json_encode([
                            'serverInfo' => ['name' => 'Mock', 'version' => '1.0'],
                            'protocolVersion' => '2025-03-26',
                            'capabilities' => [],
                        ], JSON_THROW_ON_ERROR)
                    ));
                }

                return resolve(new Response(200, ['Content-Type' => 'application/json'], ''));
            }

            public function eventDispatcherForTest(): ?EventDispatcherInterface
            {
                return $this->eventDispatcher;
            }
        };

        $eventDispatcher = new EventDispatcher();
        $transporter->initializeConnection($eventDispatcher, [], [], '2025-03-26');

        $request = (new PingRequest())->withId($requestId);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('MCP server returned Content-Type: application/json but an empty response body');

        await($transporter->sendRequest($request));
    }

    public function testSendRequestWithApplicationJsonStreamErrorRejects(): void
    {
        $requestId = 'req-err-' . bin2hex(random_bytes(4));
        $stream = new ThroughStream();
        $responseWithStream = $this->createMock(ResponseInterface::class);
        $responseWithStream->method('getBody')->willReturn($stream);
        $responseWithStream->method('getHeaderLine')->with('Content-Type')->willReturn('application/json');

        $transporter = new class ('https://example.com/mcp', $responseWithStream) extends StreamableHttpTransporter {
            private ResponseInterface $nextResponse;

            public function __construct(string $endpoint, ResponseInterface $nextResponse)
            {
                parent::__construct($endpoint);
                $this->nextResponse = $nextResponse;
            }

            protected function doSendRequest(RequestInterface $request): PromiseInterface
            {
                if ($request instanceof InitializeRequest) {
                    return resolve(new Response(
                        200,
                        ['Content-Type' => 'application/json', 'Mcp-Session-Id' => 'session-1'],
                        json_encode([
                            'serverInfo' => ['name' => 'Mock', 'version' => '1.0'],
                            'protocolVersion' => '2025-03-26',
                            'capabilities' => [],
                        ], JSON_THROW_ON_ERROR)
                    ));
                }

                return resolve($this->nextResponse);
            }

            public function eventDispatcherForTest(): ?EventDispatcherInterface
            {
                return $this->eventDispatcher;
            }
        };

        $eventDispatcher = new EventDispatcher();
        $transporter->initializeConnection($eventDispatcher, [], [], '2025-03-26');

        $request = (new PingRequest())->withId($requestId);
        $promise = $transporter->sendRequest($request);

        Loop::get()->addTimer(0, function () use ($stream): void {
            $stream->emit('error', [new \RuntimeException('Stream read error')]);
        });

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Stream read error');

        await($promise);
    }

    public function testSendRequestWithApplicationJsonStreamCloseWithoutEndRejects(): void
    {
        $requestId = 'req-close-' . bin2hex(random_bytes(4));
        $stream = new ThroughStream();
        $responseWithStream = $this->createMock(ResponseInterface::class);
        $responseWithStream->method('getBody')->willReturn($stream);
        $responseWithStream->method('getHeaderLine')->with('Content-Type')->willReturn('application/json');

        $transporter = new class ('https://example.com/mcp', $responseWithStream) extends StreamableHttpTransporter {
            private ResponseInterface $nextResponse;

            public function __construct(string $endpoint, ResponseInterface $nextResponse)
            {
                parent::__construct($endpoint);
                $this->nextResponse = $nextResponse;
            }

            protected function doSendRequest(RequestInterface $request): PromiseInterface
            {
                if ($request instanceof InitializeRequest) {
                    return resolve(new Response(
                        200,
                        ['Content-Type' => 'application/json', 'Mcp-Session-Id' => 'session-1'],
                        json_encode([
                            'serverInfo' => ['name' => 'Mock', 'version' => '1.0'],
                            'protocolVersion' => '2025-03-26',
                            'capabilities' => [],
                        ], JSON_THROW_ON_ERROR)
                    ));
                }

                return resolve($this->nextResponse);
            }

            public function eventDispatcherForTest(): ?EventDispatcherInterface
            {
                return $this->eventDispatcher;
            }
        };

        $eventDispatcher = new EventDispatcher();
        $transporter->initializeConnection($eventDispatcher, [], [], '2025-03-26');

        $request = (new PingRequest())->withId($requestId);
        $promise = $transporter->sendRequest($request);

        Loop::get()->addTimer(0, function () use ($stream): void {
            $stream->close();
        });

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Stream closed before completion');

        await($promise);
    }
}
