<?php

namespace Swis\McpClient\Schema;

/**
 * Represents a tool in the MCP schema
 *
 * @phpstan-type InputSchema array{properties: array{type: string, mixed}, required: array<string>, type: string}
 * @phpstan-type ToolData array{name: string, inputSchema?: InputSchema|array{}, description?: string, annotations?: array<string, mixed>}
 */
class Tool
{
    /**
     * @param string $name The name of the tool
     * @param array{}|InputSchema $schema The tool parameters schema
     * @param string|null $description Optional tool description
     * @param ToolAnnotation|null $annotations Optional tool annotations
     */
    public function __construct(
        protected string $name,
        protected array $schema,
        protected ?string $description = null,
        protected ?ToolAnnotation $annotations = null
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
     * Get the tool annotations
     *
     * @return ToolAnnotation|null
     */
    public function getAnnotations(): ?ToolAnnotation
    {
        return $this->annotations;
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

        if ($this->annotations !== null) {
            $data['annotations'] = $this->annotations->toArray();
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
        $annotations = null;
        if (isset($data['annotations'])) {
            $annotations = ToolAnnotation::fromArray($data['annotations']);
        }

        return new self(
            $data['name'],
            $data['inputSchema'] ?? [],
            $data['description'] ?? null,
            $annotations
        );
    }
}
