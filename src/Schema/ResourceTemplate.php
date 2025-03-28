<?php

namespace Swis\McpClient\Schema;

/**
 * Represents a resource template in the MCP schema
 *
 * @phpstan-type ResourceTemplateData array{name: string, uri: string, description?: string, mimeType?: string, annotations?: array{audience?: array<'assistant'|'user'>, priority?: float}}
 */
class ResourceTemplate
{
    /**
     * @param string $name A human-readable name for this resource template
     * @param string $uri The URI pattern for resources created from this template
     * @param string|null $description Optional description of what resources created from this template represent
     * @param string|null $mimeType Optional MIME type of resources created from this template
     * @param Annotation|null $annotations Optional annotations with audience and priority
     */
    public function __construct(
        protected string $name,
        protected string $uri,
        protected ?string $description = null,
        protected ?string $mimeType = null,
        protected ?Annotation $annotations = null
    ) {
    }

    /**
     * Get the template name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the URI pattern
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Get the template description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Get the MIME type
     *
     * @return string|null
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * Get annotations
     *
     * @return Annotation|null
     */
    public function getAnnotations(): ?Annotation
    {
        return $this->annotations;
    }

    /**
     * Convert to array
     *
     * @return ResourceTemplateData
     */
    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'uri' => $this->uri,
        ];

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        if ($this->mimeType !== null) {
            $data['mimeType'] = $this->mimeType;
        }

        if ($this->annotations !== null) {
            $data['annotations'] = $this->annotations->toArray();
        }

        return $data;
    }

    /**
     * Create from array
     *
     * @param ResourceTemplateData $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $annotations = isset($data['annotations'])
            ? Annotation::fromArray($data['annotations'])
            : null;

        return new self(
            $data['name'],
            $data['uri'],
            $data['description'] ?? null,
            $data['mimeType'] ?? null,
            $annotations
        );
    }
}
