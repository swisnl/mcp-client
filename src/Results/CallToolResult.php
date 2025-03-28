<?php

namespace Swis\McpClient\Results;

use Swis\McpClient\Exceptions\UnknownContentTypeException;
use Swis\McpClient\Schema\Content\EmbeddedResource;
use Swis\McpClient\Schema\Content\ImageContent;
use Swis\McpClient\Schema\Content\TextContent;

/**
 * DTO for the call tool result
 *
 * @phpstan-type CallToolResultData array{content: array<array{type: <'text'|'image'|'resource'>, text?: string, uri?: string, width?: int, height?: int, tags?: array<string>}>, _meta?: array}
 */
class CallToolResult extends BaseResult
{
    /**
     * @param string $requestId The request ID this result is for
     * @param array<TextContent|ImageContent|EmbeddedResource> $content The tool call content
     * @param bool $isError Whether this is an error response
     * @param array{}|null $meta Optional metadata
     */
    public function __construct(
        string $requestId,
        protected array $content,
        protected bool $isError = false,
        ?array $meta = null
    ) {
        parent::__construct($requestId, $meta);
    }

    /**
     * @return CallToolResultData
     */
    public function toArray(): array
    {
        $content = array_map(function ($content) {
            return $content->toArray();
        }, $this->content);

        $data = [
            'content' => $content,
        ];

        if ($this->isError) {
            $data['isError'] = true;
        }

        return $data;
    }

    /**
     * Get content
     *
     * @return array<TextContent|ImageContent|EmbeddedResource>
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * Check if this is an error
     *
     * @return bool
     */
    public function isError(): bool
    {
        return $this->isError;
    }

    /**
     * Create result from array
     *
     * @param CallToolResultData $data Result data
     * @param string $requestId Request ID
     * @return self
     */
    public static function fromArray(array $data, string $requestId): self
    {
        $content = array_map(fn (array $contentData) => match ($contentData['type']) {
            'text' => TextContent::fromArray($contentData),
            'image' => ImageContent::fromArray($contentData),
            'resource' => EmbeddedResource::fromArray($contentData),
            default => throw new UnknownContentTypeException('Unknown content type: ' . $contentData['type'])
        }, $data['content']);

        return new self(
            $requestId,
            $content,
            $data['isError'] ?? false,
            $data['_meta'] ?? null
        );
    }
}
