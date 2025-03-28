<?php

namespace Swis\McpClient\Schema;

/**
 * Represents a resource in the MCP schema
 *
 * @phpstan-type ResourceData array{name: string, uri: string, description?: string, mimeType?: string, size?: int, annotations?: array{audience?: array<'assistant'|'user'>, priority?: float}}
 */
class Resource
{
    /**
     * @param string $name A human-readable name for this resource
     * @param string $uri The URI of this resource
     * @param string|null $description Optional description of what this resource represents
     * @param string|null $mimeType Optional MIME type of this resource, if known
     * @param int|null $size Optional size of the raw resource content in bytes
     * @param Annotation|null $annotations Optional annotations with audience and priority
     */
    public function __construct(
        protected string $name,
        protected string $uri,
        protected ?string $description = null,
        protected ?string $mimeType = null,
        protected ?int $size = null,
        protected ?Annotation $annotations = null
    ) {
    }

    /**
     * Get the resource name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the resource URI
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Get the resource description
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
     * Get the resource size in bytes
     *
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
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
     * @return ResourceData
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

        if ($this->size !== null) {
            $data['size'] = $this->size;
        }

        if ($this->annotations !== null) {
            $data['annotations'] = $this->annotations->toArray();
        }

        return $data;
    }

    /**
     * Create from array
     *
     * @param ResourceData $data
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
            $data['size'] ?? null,
            $annotations
        );
    }
}
