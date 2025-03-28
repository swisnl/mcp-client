# Model Context Protocol client implementation for PHP

[![PHP from Packagist](https://img.shields.io/packagist/php-v/swisnl/mcp-client.svg)](https://packagist.org/packages/swisnl/mcp-client)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/swisnl/mcp-client.svg)](https://packagist.org/packages/swisnl/mcp-client)
[![Software License](https://img.shields.io/packagist/l/swisnl/mcp-client.svg)](LICENSE.md)
[![Buy us a tree](https://img.shields.io/badge/Treeware-%F0%9F%8C%B3-lightgreen.svg)](https://plant.treeware.earth/swisnl/mcp-client)
[![Build Status](https://img.shields.io/github/checks-status/swisnl/mcp-client/master?label=tests)](https://github.com/swisnl/mcp-client/actions/workflows/tests.yml)
[![Made by SWIS](https://img.shields.io/badge/%F0%9F%9A%80-made%20by%20SWIS-%230737A9.svg)](https://www.swis.nl)

A PHP client library for interacting with Model Context Protocol (MCP) servers.

## Installation

You can install the package via composer:

```bash
composer require swisnl/mcp-client
```

## Requirements

- PHP 8.1 or higher
- [ReactPHP](https://reactphp.org/) packages

## Features

- Multiple transport mechanisms:
  - SSE (Server-Sent Events)
  - Stdio (Standard input/output)
  - Process (External process communication)
- Promise-based API with ReactPHP
- PSR-3 Logger interface support
- Most of MCP protocol support

## Basic Usage

### SSE Transport

```php
use Swis\McpClient\Client;

// Create client with SSE transporter
$endpoint = 'https://your-mcp-server.com/sse';
$client = Client::withSse($endpoint);

// Connect to the server
$client->connect(function($initResponse) {
    echo "Connected to server: " . json_encode($initResponse['serverInfo']) . "\n";
});

// List available tools
$tools = $client->listTools();
foreach ($tools->getTools() as $tool) {
    echo "- {$tool->getName()}: {$tool->getDescription()}\n";
}

// Call a tool
$result = $client->callTool('echo', ['message' => 'Hello World!']);
echo $result->getResult() . "\n";
```

### Process Transport

```php
use Swis\McpClient\Client;

// Create client with a process transporter
[$client, $process] = Client::withProcess('/path/to/mcp-server/binary');

// Connect to the server
$client->connect();

// Use the client...

// Disconnect when done
$client->disconnect();
```

## Use in combination with Agents SDK

First, install Agents SDK

```bash
composer require swisnl/agents-sdk
```

```php
use Swis\Agents\Agent;
use Swis\Agents\Mcp\McpConnection;
use Swis\McpClient\Client;
use Swis\Agents\Orchestrator;

$agent = new Agent(
    name: 'Calculator Agent',
    description: 'This Agent can perform arithmetic operations.',
    mcpConnections: [
        new MathMcpConnection(),
    ]
);

$orchestrator = new Orchestrator($agent);
echo $orchestrator
        ->withUserInstruction('What\'s 5 + 5?')
        ->run($agent)

class MathMcpConnection extends McpConnection
{
    public function __construct()
    {
        [$client, $process] = Client::withProcess(
            command: 'node ' . realpath(__DIR__ . '/node_modules/math-mcp/build/index.js'),
        );

        parent::__construct(
            client: $client,
            name: 'Math MCP',
        );
    }
}
```

## Advanced Usage

### Custom Transporter

You can implement your own transporter by implementing the `TransporterInterface`:

```php
use Swis\McpClient\TransporterInterface;
use Swis\McpClient\EventDispatcher;

class CustomTransporter implements TransporterInterface
{
    // Implement required methods
}

// Create a client with your custom transporter
$transporter = new CustomTransporter();
$eventDispatcher = new EventDispatcher();
$client = new Client($transporter, $eventDispatcher);
```

### Async Operations

The client supports async operations using ReactPHP promises:

```php
$client->sendRequest(new ListToolsRequest())->then(...);
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Joris Meijer](https://github.com/jormeijer)
- [All Contributors](../../contributors)

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
