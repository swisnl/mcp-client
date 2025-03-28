<?php

namespace Swis\McpClient\Schema;

/**
 * Represents an annotation in the MCP schema
 *
 * @phpstan-type AnnotationData array{audience?: array<'assistant'|'user'>, priority?: float}
 */
class Annotation
{
    /**
     * @param Role[]|null $audience Optional audience array
     * @param float|null $priority Optional priority (0-1)
     */
    public function __construct(
        protected ?array $audience = null,
        protected ?float $priority = null
    ) {
    }

    /**
     * Get the audience
     *
     * @return Role[]|null
     */
    public function getAudience(): ?array
    {
        return $this->audience;
    }

    /**
     * Get the priority
     *
     * @return float|null
     */
    public function getPriority(): ?float
    {
        return $this->priority;
    }

    /**
     * Convert to array
     *
     * @return AnnotationData
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->audience !== null) {
            $data['audience'] = array_map(function (Role $role) {
                return $role->value;
            }, $this->audience);
        }

        if ($this->priority !== null) {
            $data['priority'] = $this->priority;
        }

        return $data;
    }

    /**
     * Create from array
     *
     * @param AnnotationData $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $audience = null;
        if (isset($data['audience'])) {
            $audience = array_map(function (string $role) {
                return Role::from($role);
            }, $data['audience']);
        }

        return new self(
            $audience,
            $data['priority'] ?? null
        );
    }
}
