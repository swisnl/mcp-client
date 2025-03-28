<?php

namespace Swis\McpClient\Requests;

/**
 * DTO for the initialize request
 */
class InitializeRequest extends BaseRequest
{
    /**
     * @param array<string, array<string, scalar|bool>> $capabilities The client's capabilities
     * @param array<string, string> $clientInfo Information about the client
     * @param string $protocolVersion The protocol version to use
     */
    public function __construct(
        protected array $capabilities,
        protected array $clientInfo,
        protected string $protocolVersion = '2024-11-05',
    ) {
        $this->method = 'initialize';

        // Set default capabilities
        $this->capabilities = array_merge([
            'roots' => ['listChanged' => false],
            'sampling' => new \StdClass(),
        ], $capabilities);

        // Set default client info
        $this->clientInfo = array_merge([
            'name' => 'PHP MCP Client',
            'version' => '1.0.0',
        ], $clientInfo);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'capabilities' => $this->capabilities,
            'clientInfo' => $this->clientInfo,
            'protocolVersion' => $this->protocolVersion,
        ];
    }
}
