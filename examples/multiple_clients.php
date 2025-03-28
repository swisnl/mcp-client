<?php

require __DIR__ . '/../vendor/autoload.php';

use Swis\McpClient\Client;
use Psr\Log\LoggerInterface;
use Psr\Log\AbstractLogger;
use React\EventLoop\Loop;

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

$logger = new ConsoleLogger();
$mockServerPath = realpath(__DIR__ . '/../tests/Mock/server.php');
$phpPath = PHP_BINARY;

$command = "$phpPath $mockServerPath";

[$client1, $process1] = Client::withProcess(
    $command,
    logger: $logger
);

[$client2, $process2] = Client::withProcess(
    $command,
    logger: $logger
);

$client1->connect();
$client2->connect();

echo "Both clients connected successfully\n";

$client1->listTools();
echo "Client 1 listTools successful\n";

$client2->listTools();
echo "Client 2 listTools successful\n";

echo "Disconnecting client 1...\n";
$client1->disconnect();
echo "Client 1 disconnected\n";

$client2->listTools();
echo "Client 2 listTools successful after client 1 disconnect\n";

echo "Disconnecting client 2...\n";
$client2->disconnect();
echo "Client 2 disconnected\n";

// Clean up processes
if (is_resource($process1)) {
    proc_close($process1);
}

if (is_resource($process2)) {
    proc_close($process2);
}

// Tell the system not to auto-run the loop on shutdown
Loop::stop();

echo "Test completed\n";
