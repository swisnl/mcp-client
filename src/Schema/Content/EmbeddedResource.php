<?php

namespace Swis\McpClient\Schema\Content;

use Swis\McpClient\Exceptions\UnknownResourceTypeException;
use Swis\McpClient\Schema\Annotation;
use Swis\McpClient\Schema\Resource\BlobResourceContents;
use Swis\McpClient\Schema\Resource\TextResourceContents;

/**
 * Represents an embedded resource in the MCP schema
 *
 * @phpstan-type EmbeddedResourceData array{type: 'resource', resource: array{blob: string, uri: string, mimeType?: string}|array{text: string, uri: string, mimeType?: string}, annotations?: array{audience?: array<'assistant'|'user'>, priority?: float}}
 */
class EmbeddedResource
{
    /**
     * @param BlobResourceContents|TextResourceContents $resource The resource contents
     * @param Annotation|null $annotations Optional annotations
     */
    public function __construct(
        protected BlobResourceContents|TextResourceContents $resource,
        protected ?Annotation $annotations = null
    ) {
    }

    /**
     * Get the resource
     *
     * @return BlobResourceContents|TextResourceContents
     */
    public function getResource(): BlobResourceContents|TextResourceContents
    {
        return $this->resource;
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
     * @return EmbeddedResourceData
     */
    public function toArray(): array
    {
        $data = [
            'type' => 'resource',
            'resource' => $this->resource->toArray(),
        ];

        if ($this->annotations !== null) {
            $data['annotations'] = $this->annotations->toArray();
        }

        return $data;
    }

    /**
     * Create from array
     *
     * @param EmbeddedResourceData $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $resourceData = $data['resource'];

        if (isset($resourceData['text'])) {
            $resource = TextResourceContents::fromArray($resourceData);
        } elseif (isset($resourceData['blob'])) {
            $resource = BlobResourceContents::fromArray($resourceData);
        } else {
            throw new UnknownResourceTypeException('Unknown resource type');
        }

        $annotations = isset($data['annotations'])
            ? Annotation::fromArray($data['annotations'])
            : null;

        return new self(
            $resource,
            $annotations
        );
    }
}
