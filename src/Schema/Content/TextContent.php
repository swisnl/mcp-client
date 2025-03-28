<?php

namespace Swis\McpClient\Schema\Content;

use Swis\McpClient\Schema\Annotation;

/**
 * Represents the contents of a text resource in the MCP schema
 *
 * @phpstan-type TextContentData array{type: 'text', text: string, annotations?: array{audience?: array<'assistant'|'user'>, priority?: float}}
 */
class TextContent
{
    /**
     * @param string $text The text content
     * @param Annotation|null $annotations Optional annotations with audience and priority
     */
    public function __construct(
        protected string $text,
        protected ?Annotation $annotations = null
    ) {
    }

    /**
     * Get the text content
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
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
     * @return TextContentData
     */
    public function toArray(): array
    {
        $data = [
            'type' => 'text',
            'text' => $this->text,
        ];

        if ($this->annotations !== null) {
            $data['annotations'] = $this->annotations->toArray();
        }

        return $data;
    }

    /**
     * Create from array
     *
     * @param TextContentData $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $annotations = isset($data['annotations'])
            ? Annotation::fromArray($data['annotations'])
            : null;

        return new self(
            $data['text'],
            $annotations
        );
    }
}
