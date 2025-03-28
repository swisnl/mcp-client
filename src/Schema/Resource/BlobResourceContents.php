<?php

namespace Swis\McpClient\Schema\Resource;

/**
 * Represents the contents of a blob resource in the MCP schema
 *
 * @phpstan-type BlobResourceContentsData array{blob: string, uri: string, mimeType?: string}
 */
class BlobResourceContents
{
    /**
     * @param string $blob Base64-encoded binary data
     * @param string $uri The URI of the resource
     * @param string|null $mimeType Optional MIME type
     */
    public function __construct(
        protected string $blob,
        protected string $uri,
        protected ?string $mimeType = null
    ) {
    }

    /**
     * Get the blob data
     *
     * @return string
     */
    public function getBlob(): string
    {
        return $this->blob;
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
     * @return BlobResourceContentsData
     */
    public function toArray(): array
    {
        $data = [
            'blob' => $this->blob,
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
     * @param BlobResourceContentsData $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['blob'],
            $data['uri'],
            $data['mimeType'] ?? null
        );
    }
}
