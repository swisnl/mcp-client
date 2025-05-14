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

// Create client with Streamable HTTP transporter and the logger
$endpoint = 'http://localhost:3000/mcp'; // Replace with actual SSE endpoint
$client = Client::withStreamableHttp($endpoint, $logger);

$client->connect(function($initResponse) use ($logger) {
    $logger->info('Connected to server', [
        'protocolVersion' => $initResponse['protocolVersion'] ?? 'Not provided',
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
