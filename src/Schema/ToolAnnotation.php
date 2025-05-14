<?php

namespace Swis\McpClient\Schema;

/**
 * Represents tool annotations in the MCP schema
 *
 * @phpstan-type ToolAnnotationData array{destructiveHint?: bool, idempotentHint?: bool, openWorldHint?: bool, readOnlyHint?: bool, title?: string}
 */
class ToolAnnotation
{
    /**
     * @param bool|null $destructiveHint If true, the tool may perform destructive updates to its environment
     * @param bool|null $idempotentHint If true, calling the tool repeatedly with the same arguments will have no additional effect
     * @param bool|null $openWorldHint If true, this tool may interact with an "open world" of external entities
     * @param bool|null $readOnlyHint If true, the tool does not modify its environment
     * @param string|null $title A human-readable title for the tool
     */
    public function __construct(
        protected ?bool $destructiveHint = null,
        protected ?bool $idempotentHint = null,
        protected ?bool $openWorldHint = null,
        protected ?bool $readOnlyHint = null,
        protected ?string $title = null
    ) {
    }

    /**
     * Get the destructive hint
     *
     * @return bool|null
     */
    public function getDestructiveHint(): ?bool
    {
        return $this->destructiveHint;
    }

    /**
     * Get the idempotent hint
     *
     * @return bool|null
     */
    public function getIdempotentHint(): ?bool
    {
        return $this->idempotentHint;
    }

    /**
     * Get the open world hint
     *
     * @return bool|null
     */
    public function getOpenWorldHint(): ?bool
    {
        return $this->openWorldHint;
    }

    /**
     * Get the read only hint
     *
     * @return bool|null
     */
    public function getReadOnlyHint(): ?bool
    {
        return $this->readOnlyHint;
    }

    /**
     * Get the title
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Convert to array
     *
     * @return ToolAnnotationData
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->destructiveHint !== null) {
            $data['destructiveHint'] = $this->destructiveHint;
        }

        if ($this->idempotentHint !== null) {
            $data['idempotentHint'] = $this->idempotentHint;
        }

        if ($this->openWorldHint !== null) {
            $data['openWorldHint'] = $this->openWorldHint;
        }

        if ($this->readOnlyHint !== null) {
            $data['readOnlyHint'] = $this->readOnlyHint;
        }

        if ($this->title !== null) {
            $data['title'] = $this->title;
        }

        return $data;
    }

    /**
     * Create from array
     *
     * @param ToolAnnotationData $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['destructiveHint'] ?? null,
            $data['idempotentHint'] ?? null,
            $data['openWorldHint'] ?? null,
            $data['readOnlyHint'] ?? null,
            $data['title'] ?? null
        );
    }
}
