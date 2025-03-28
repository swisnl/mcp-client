<?php

require __DIR__ . '/../vendor/autoload.php';

use Swis\McpClient\Client;
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

// Create a logger
$logger = new ConsoleLogger();

// Create a client connected to a child process
[$client, $process] = Client::withProcess(
    // eg: '/opt/homebrew/bin/node ' . realpath(__DIR__ . '/../../math-mcp/build/index.js'),
    command: 'command/to/execute --with-arguments',
    env: ['ENV_VAR' => 'value'],
    logger: $logger
);

// Alternatively, you can use the ProcessFactory directly:
/*
use Swis\McpClient\Factory\ProcessFactory;

[$transporter, $process] = ProcessFactory::createTransporterForProcess(
    command: 'command/to/execute --with-arguments',
    env: ['ENV_VAR' => 'value'],
    logger: $logger
);

$eventDispatcher = new \Swis\McpClient\EventDispatcher();
$client = new \Swis\McpClient\Client($transporter, $eventDispatcher, $logger);
*/

// Note: If you need to connect to an existing process with custom streams,
// you can also use the StdioTransporter directly:
/*
$inputStream = fopen('path/to/read/from', 'r');  // The stream to READ from (server's output)
$outputStream = fopen('path/to/write/to', 'w');  // The stream to WRITE to (server's input)

$transporter = new \Swis\McpClient\Transporters\StdioTransporter(
    $inputStream,
    $outputStream,
    true, // Close streams on disconnect
    $logger
);

$eventDispatcher = new \Swis\McpClient\EventDispatcher();
$client = new \Swis\McpClient\Client($transporter, $eventDispatcher, $logger);
*/

// Connect to the server process
$client->connect(function($initResponse) use ($logger) {
    $logger->info('Connected to server process', [
        'protocolVersion' => $initResponse['protocolVersion'],
        'serverInfo' => $initResponse['serverInfo'] ?? 'Not provided'
    ]);
});

$tools = $client->listTools();
echo "Available tools:\n";

if (empty($tools->getTools())) {
    echo "No tools available\n";
    return;
}

foreach ($tools->getTools() as $tool) {
    echo "- {$tool->getName()}: {$tool->getDescription()}\n";
}
