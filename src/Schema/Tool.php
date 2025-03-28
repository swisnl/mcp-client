<?php

namespace Swis\McpClient\Schema;

/**
 * Represents a tool in the MCP schema
 *
 * @phpstan-type InputSchema array{properties: array{type: string, mixed}, required: array<string>, type: string}
 * @phpstan-type ToolData array{name: string, inputSchema?: InputSchema|array{}, description?: string}
 */
class Tool
{
    /**
     * @param string $name The name of the tool
     * @param array{}|InputSchema $schema The tool parameters schema
     * @param string|null $description Optional tool description
     */
    public function __construct(
        protected string $name,
        protected array $schema,
        protected ?string $description = null
    ) {
    }

    /**
     * Get the tool name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the tool parameters schema
     *
     * @return array{}|InputSchema
     */
    public function getSchema(): array
    {
        return $this->schema;
    }

    /**
     * Get the tool description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Convert to array
     *
     * @return ToolData
     */
    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'inputSchema' => $this->schema,
        ];

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        return $data;
    }

    /**
     * Create from array
     *
     * @param ToolData $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['inputSchema'] ?? [],
            $data['description'] ?? null
        );
    }
}
