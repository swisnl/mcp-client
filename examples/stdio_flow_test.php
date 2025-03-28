<?php

/**
 * This example demonstrates the full client flow with a StdioTransporter
 * connected to a mock server process.
 * 
 * It shows how to:
 * 1. Create a client with a StdioTransporter
 * 2. Connect to a server process
 * 3. Initialize the connection
 * 4. Send requests and handle responses
 * 5. Call tools and use the results
 * 6. Disconnect gracefully
 */

require __DIR__ . '/../vendor/autoload.php';

use Swis\McpClient\Client;
use Swis\McpClient\Requests\CallToolRequest;
use Psr\Log\LoggerInterface;
use Psr\Log\AbstractLogger;

// Simple logger that outputs to console
class ConsoleLogger extends AbstractLogger implements LoggerInterface
{
    public function log($level, $message, array $context = []): void
    {
        echo "[$level] $message\n";
        if (!empty($context)) {
            echo "Context: " . json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n\n";
        }
    }
}

echo "Starting MCP Client flow test with StdioTransporter\n\n";

// Create a logger
$logger = new ConsoleLogger();

$mockServerPath = realpath(__DIR__ . '/../tests/Mock/server.php');

// Start by launching the mock server as a child process
echo "Starting mock server process...\n";
// Use the full PHP path to ensure it works
$phpPath = PHP_BINARY; // This gives the full path to the PHP executable
[$client, $process] = Client::withProcess(
    "$phpPath $mockServerPath",
    logger: $logger
);

// Set custom client info
$client->withClientInfo([
    'name' => 'StdioFlow Test Client',
    'version' => '1.0.0-test',
]);

// Connect to the server
echo "Connecting to server...\n";
try {
    $client->connect(function($initResponse) use ($logger) {
        echo "Connected successfully! Server info:\n";
        if (isset($initResponse['serverInfo'])) {
            echo sprintf("- Name: %s\n", $initResponse['serverInfo']['name'] ?? 'Unknown');
            echo sprintf("- Version: %s\n", $initResponse['serverInfo']['version'] ?? 'Unknown');
        } else {
            echo "- No server info available\n";
        }
        echo sprintf("- Protocol: %s\n\n", $initResponse['protocolVersion'] ?? 'Unknown');
    });
} catch (\Exception $e) {
    echo "Error connecting to server: {$e->getMessage()}\n";
    exit(1);
}

// Ping the server
echo "Testing ping...\n";
try {
    $pingResult = $client->ping();
    echo "Ping result: " . ($pingResult ? "Success" : "Failed") . "\n\n";
} catch (\Exception $e) {
    echo "Error pinging server: {$e->getMessage()}\n\n";
}

// List available tools
echo "Getting available tools...\n";
try {
    $tools = $client->listTools();
    echo "Available tools:\n";
    foreach ($tools->getTools() as $tool) {
        echo "- {$tool->getName()}: {$tool->getDescription()}\n";
        if (!empty($tool->getSchema())) {
            echo "  Schema:\n";
            echo json_encode($tool->getSchema());
        }
    }
    echo "\n";
} catch (\Exception $e) {
    echo "Error listing tools: {$e->getMessage()}\n\n";
}

// Use the echo tool
echo "Testing the echo tool...\n";
try {
    $echoRequest = new CallToolRequest(
        'test.echo',
        ['message' => 'Hello from the StdioTransporter test!']
    );
    $echoResult = $client->callTool($echoRequest);
    echo "Echo tool response: {$echoResult->getContent()[0]->getText()}\n\n";
} catch (\Exception $e) {
    echo "Error calling echo tool: {$e->getMessage()}\n\n";
}

// Use the add tool
echo "Testing the add tool...\n";
try {
    $addRequest = new CallToolRequest(
        'test.add',
        ['a' => 7, 'b' => 35]
    );
    $addResult = $client->callTool($addRequest);
    echo "7 + 35 = {$addResult->getContent()[0]->getText()}\n\n";
} catch (\Exception $e) {
    echo "Error calling add tool: {$e->getMessage()}\n\n";
}

// Test an error case
echo "Testing a non-existent tool...\n";
try {
    $errorRequest = new CallToolRequest(
        'test.nonexistent',
        ['foo' => 'bar']
    );
    $errorResult = $client->callTool($errorRequest);
    if ($errorResult instanceof \Swis\McpClient\Results\JsonRpcError) {
        echo "Error received (as expected):\n";
        echo "- Code: {$errorResult->getCode()}\n";
        echo "- Message: {$errorResult->getMessage()}\n\n";
    } else {
        echo "Unexpected result: " . print_r($errorResult, true) . "\n\n";
    }
} catch (\Exception $e) {
    echo "Exception: {$e->getMessage()}\n\n";
}

// Disconnect from the server
echo "Disconnecting from server...\n";
$client->disconnect();

// Terminate the server process
echo "Terminating server process...\n";
proc_terminate($process);
proc_close($process);

echo "Test completed successfully!\n";