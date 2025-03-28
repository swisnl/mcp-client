<?php

namespace Swis\McpClient\Tests\Mock;

use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;

/**
 * A mock MCP server implementation for testing purposes.
 *
 * This class simulates a simple MCP server by responding to incoming JSON-RPC requests.
 * It listens on STDIN and responds on STDOUT, making it easy to use with the StdioTransporter.
 */
class MockServer
{
    /**
     * @var resource Input stream (STDIN)
     */
    private $input;

    /**
     * @var resource Output stream (STDOUT)
     */
    private $output;

    /**
     * @var ReadableResourceStream
     */
    private ReadableResourceStream $readableStream;

    /**
     * @var WritableResourceStream
     */
    private WritableResourceStream $writableStream;

    /**
     * @var LoopInterface The event loop
     */
    private LoopInterface $loop;

    /**
     * @var string Buffer for incoming data
     */
    private string $buffer = '';

    /**
     * @var array Available tools to report
     */
    private array $tools = [];

    /**
     * Constructor
     */
    public function __construct(?LoopInterface $loop = null)
    {
        $this->loop = $loop ?? Loop::get();

        // Use standard input and output
        $this->input = fopen('php://stdin', 'r');
        $this->output = fopen('php://stdout', 'w');

        // Set to non-blocking mode
        stream_set_blocking($this->input, false);
        stream_set_blocking($this->output, false);

        // Create ReactPHP streams
        $this->readableStream = new ReadableResourceStream($this->input, $this->loop);
        $this->writableStream = new WritableResourceStream($this->output, $this->loop);

        // Setup event listeners
        $this->setupListeners();
    }

    /**
     * Add a mock tool to the server
     *
     * @param string $name Tool name
     * @param string $description Tool description
     * @param array $parameters Tool parameters schema
     * @return self
     */
    public function withTool(string $name, string $description, array $parameters = []): self
    {
        $this->tools[] = [
            'name' => $name,
            'description' => $description,
            'inputSchema' => $parameters,
        ];

        return $this;
    }

    /**
     * Start the server
     */
    public function run(): void
    {
        $this->loop->run();
    }

    /**
     * Setup event listeners for the input/output streams
     */
    private function setupListeners(): void
    {
        $this->readableStream->on('data', function (string $data) {
            $this->buffer .= $data;
            $this->processBuffer();
        });

        $this->readableStream->on('error', function (\Throwable $e) {
            fwrite(STDERR, 'Error in input stream: ' . $e->getMessage() . PHP_EOL);
        });

        $this->readableStream->on('close', function () {
            fwrite(STDERR, 'Input stream closed' . PHP_EOL);
            $this->loop->stop();
        });
    }

    /**
     * Process the buffer for complete JSON messages
     */
    private function processBuffer(): void
    {
        // Process each line as a separate JSON message
        while (($pos = strpos($this->buffer, "\n")) !== false) {
            $line = substr($this->buffer, 0, $pos);
            $this->buffer = substr($this->buffer, $pos + 1);

            if (empty(trim($line))) {
                continue;
            }

            try {
                $request = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
                $this->handleRequest($request);
            } catch (\JsonException $e) {
                fwrite(STDERR, 'Failed to decode request: ' . $e->getMessage() . PHP_EOL);
            }
        }
    }

    /**
     * Handle incoming JSON-RPC request
     *
     * @param array $request Request data
     */
    private function handleRequest(array $request): void
    {
        $id = $request['id'] ?? null;
        $method = $request['method'] ?? '';
        $params = $request['params'] ?? [];

        // Only handle requests with an ID and method
        if (! $id || ! $method) {
            return;
        }

        // Prepare a basic response
        $response = [
            'jsonrpc' => '2.0',
            'id' => $id,
        ];

        // Handle different method types
        switch ($method) {
            case 'initialize':
                $response['result'] = [
                    'serverInfo' => [
                        'name' => 'Mock MCP Server',
                        'version' => '1.0.0',
                    ],
                    'protocolVersion' => '2024-11-05',
                    'capabilities' => [
                        'roots' => ['listChanged' => false],
                    ],
                ];

                break;

            case 'notifications/initialized':
                // This is a notification, no response needed
                return;

            case 'tools/list':
                $response['result'] = [
                    'tools' => $this->tools,
                ];

                break;

            case 'prompts/list':
                $response['result'] = [
                    'prompts' => [],
                ];

                break;

            case 'resources/list':
                $response['result'] = [
                    'resources' => [],
                ];

                break;

            case 'list_resource_templates':
                $response['result'] = [
                    'resourceTemplates' => [],
                ];

                break;

            case 'ping':
                $response['result'] = true;

                break;

            case 'tools/call':
                // Handle tool calls
                $toolName = $params['name'] ?? '';
                $toolParams = $params['arguments'] ?? [];

                if ($toolName === 'test.echo') {
                    $response['result'] = [
                        'content' => [
                            ['type' => 'text', 'text' => $toolParams['message'] ?? 'No message provided'],
                        ],
                    ];
                } elseif ($toolName === 'test.add') {
                    $a = $toolParams['a'] ?? 0;
                    $b = $toolParams['b'] ?? 0;
                    $response['result'] = [
                        'content' => [
                            ['type' => 'text', 'text' => $a + $b],
                        ],
                    ];
                } else {
                    $response['error'] = [
                        'code' => -32601,
                        'message' => "Tool not found: $toolName",
                    ];
                }

                break;

            default:
                // For any other method, return an empty result
                $response['result'] = null;
        }

        // Send the response
        $this->sendResponse($response);
    }

    /**
     * Send response to the client
     *
     * @param array $response Response data
     */
    private function sendResponse(array $response): void
    {
        $json = json_encode($response) . "\n";
        $this->writableStream->write($json);
    }
}
