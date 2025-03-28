<?php

namespace Swis\McpClient\Schema\Content;

use Swis\McpClient\Schema\Annotation;

/**
 * Represents the contents of an image resource in the MCP schema
 *
 * @phpstan-type ImageContentData array{type: 'image', data: string, mimeType: string, annotations?: array{audience?: array<'assistant'|'user'>, priority?: float}}
 */
class ImageContent
{
    /**
     * @param string $data Base64-encoded image data
     * @param string $mimeType MIME type of the image
     * @param Annotation|null $annotations Optional annotations with audience and priority
     */
    public function __construct(
        protected string $data,
        protected string $mimeType,
        protected ?Annotation $annotations = null
    ) {
    }

    /**
     * Get the base64-encoded image data
     *
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * Get the MIME type
     *
     * @return string
     */
    public function getMimeType(): string
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
     * @return ImageContentData
     */
    public function toArray(): array
    {
        $data = [
            'type' => 'image',
            'data' => $this->data,
            'mimeType' => $this->mimeType,
        ];

        if ($this->annotations !== null) {
            $data['annotations'] = $this->annotations->toArray();
        }

        return $data;
    }

    /**
     * Create from array
     *
     * @param ImageContentData $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $annotations = isset($data['annotations'])
            ? Annotation::fromArray($data['annotations'])
            : null;

        return new self(
            $data['data'],
            $data['mimeType'],
            $annotations
        );
    }
}
