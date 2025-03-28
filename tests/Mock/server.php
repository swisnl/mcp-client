<?php

// Server script for integration tests
require __DIR__ . '/../../vendor/autoload.php';

use Swis\McpClient\Tests\Mock\MockServer;

// Create a mock server with test tools
$server = new MockServer();

// Add a couple of test tools
$server->withTool(
    'test.echo',
    'Echo the input parameters back as the result',
    [
        'message' => [
            'type' => 'string',
            'description' => 'The message to echo',
        ],
    ]
)->withTool(
    'test.add',
    'Add two numbers together',
    [
        'a' => [
            'type' => 'number',
            'description' => 'First number',
        ],
        'b' => [
            'type' => 'number',
            'description' => 'Second number',
        ],
    ]
);

// Run the server
$server->run();
