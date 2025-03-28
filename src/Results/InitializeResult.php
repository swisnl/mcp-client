<?php

namespace Swis\McpClient\Results;

/**
 * DTO for the initialize result
 *
 * @phpstan-type InitializeResultData array{capabilities: array<string, string>, protocolVersion: string, serverInfo: array<string, string>, instructions?: array, _meta?: array}
 */
class InitializeResult extends BaseResult
{
    /**
     * @param string $requestId The request ID this result is for
     * @param array<string, string> $capabilities The server capabilities
     * @param string $protocolVersion The protocol version
     * @param array<string, string> $serverInfo Information about the server
     * @param array{}|null $instructions Optional instructions for the client
     * @param array{}|null $meta Optional metadata
     */
    public function __construct(
        string $requestId,
        protected array $capabilities,
        protected string $protocolVersion,
        protected array $serverInfo,
        protected ?array $instructions = null,
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
     * @return array
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
     * @return array
     */
    public function getServerInfo(): array
    {
        return $this->serverInfo;
    }

    /**
     * Get instructions
     *
     * @return array{}|null
     */
    public function getInstructions(): ?array
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
