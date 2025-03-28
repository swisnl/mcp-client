<?php

namespace Swis\McpClient\Schema\Resource;

/**
 * Represents the contents of a text resource in the MCP schema
 *
 * @phpstan-type TextResourceContentsData array{text: string, uri: string, mimeType?: string}
 */
class TextResourceContents
{
    /**
     * @param string $text The text content of the resource
     * @param string $uri The URI of the resource
     * @param string|null $mimeType Optional MIME type
     */
    public function __construct(
        protected string $text,
        protected string $uri,
        protected ?string $mimeType = null
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
     * Get the URI
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
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
     * Convert to array
     *
     * @return TextResourceContentsData
     */
    public function toArray(): array
    {
        $data = [
            'text' => $this->text,
            'uri' => $this->uri,
        ];

        if ($this->mimeType !== null) {
            $data['mimeType'] = $this->mimeType;
        }

        return $data;
    }

    /**
     * Create from array
     *
     * @param TextResourceContentsData $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['text'],
            $data['uri'],
            $data['mimeType'] ?? null
        );
    }
}
