<?php

namespace Swis\McpClient\Results;

/**
 * DTO for the initialize result
 *
 * @phpstan-import-type Meta from BaseResult
 * @phpstan-type ServerCapabilities array{completions?: array<string, string>, experimental?: array{additionalProperties: array<string, string>}, logging?: array<string, string>, prompts?: array{listChanged?: bool}, resources?: array{listChanged?: bool, subscribe?: bool}, tools?: array{listChanged?: bool}}
 * @phpstan-type ServerInfo array{name: string, version: string}
 * @phpstan-type InitializeResultData array{capabilities: ServerCapabilities, protocolVersion: string, serverInfo: ServerInfo, instructions?: string, _meta?: Meta}
 */
class InitializeResult extends BaseResult
{
    /**
     * @param string $requestId The request ID this result is for
     * @param ServerCapabilities $capabilities The server capabilities
     * @param string $protocolVersion The protocol version
     * @param ServerInfo $serverInfo Information about the server
     * @param string|null $instructions Optional instructions for the client
     * @param Meta|null $meta Optional metadata
     */
    public function __construct(
        string $requestId,
        protected array $capabilities,
        protected string $protocolVersion,
        protected array $serverInfo,
        protected ?string $instructions = null,
        ?array $meta = null
    ) {
        parent::__construct($requestId, $meta);
    }

    /**
     * @return InitializeResultData
     */
    public function toArray(): array
    {
        $data = [
            'capabilities' => $this->capabilities,
            'protocolVersion' => $this->protocolVersion,
            'serverInfo' => $this->serverInfo,
        ];

        if ($this->instructions !== null) {
            $data['instructions'] = $this->instructions;
        }

        return $data;
    }

    /**
     * Get server capabilities
     *
     * @return ServerCapabilities
     */
    public function getCapabilities(): array
    {
        return $this->capabilities;
    }

    /**
     * Get protocol version
     *
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * Get server information
     *
     * @return ServerInfo
     */
    public function getServerInfo(): array
    {
        return $this->serverInfo;
    }

    /**
     * Get instructions
     *
     * @return string|null
     */
    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    /**
     * Create result from array
     *
     * @param InitializeResultData $data Result data
     * @param string $requestId Request ID
     * @return self
     */
    public static function fromArray(array $data, string $requestId): self
    {
        return new self(
            $requestId,
            $data['capabilities'],
            $data['protocolVersion'],
            $data['serverInfo'],
            $data['instructions'] ?? null,
            $data['_meta'] ?? null
        );
    }
}
