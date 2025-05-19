<?php

namespace Swis\McpClient\Schema;

use Swis\McpClient\Exceptions\UnknownContentTypeException;
use Swis\McpClient\Schema\Content\EmbeddedResource;
use Swis\McpClient\Schema\Content\ImageContent;
use Swis\McpClient\Schema\Content\TextContent;

/**
 * Represents a prompt message in the MCP schema
 *
 * @phpstan-import-type TextContentData from \Swis\McpClient\Schema\Content\TextContent
 * @phpstan-import-type ImageContentData from \Swis\McpClient\Schema\Content\ImageContent
 * @phpstan-import-type EmbeddedResourceData from \Swis\McpClient\Schema\Content\EmbeddedResource
 * @phpstan-type PromptMessageData array{role: 'assistant'|'user', content: TextContentData|ImageContentData|EmbeddedResourceData}
 */
class PromptMessage
{
    /**
     * @param Role $role Describes the role of the resource (e.g., "user", "assistant")
     * @param TextContent|ImageContent|EmbeddedResource $content The content of the resource
     */
    public function __construct(
        protected Role $role,
        protected TextContent|ImageContent|EmbeddedResource $content,
    ) {
    }

    /**
     * Get the role of the message
     *
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * Get the content of the message
     *
     * @return TextContent|ImageContent|EmbeddedResource
     */
    public function getUri(): TextContent|ImageContent|EmbeddedResource
    {
        return $this->content;
    }

    /**
     * Convert to array
     *
     * @return PromptMessageData
     */
    public function toArray(): array
    {
        return [
            'role' => $this->role->value,
            'content' => $this->content->toArray(),
        ];
    }

    /**
     * Create from array
     *
     * @param PromptMessageData $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $content = match ($data['content']['type']) {
            'text' => TextContent::fromArray($data['content']),
            'image' => ImageContent::fromArray($data['content']),
            'resource' => EmbeddedResource::fromArray($data['content']),
            default => throw new UnknownContentTypeException('Unknown content type: ' . $data['content']['type'])
        };

        return new self(
            Role::from($data['role']),
            $content
        );
    }
}
