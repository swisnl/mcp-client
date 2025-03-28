<?php

namespace Swis\McpClient\Results;

use Swis\McpClient\Exceptions\UnknownContentTypeException;
use Swis\McpClient\Schema\Content\EmbeddedResource;
use Swis\McpClient\Schema\Content\ImageContent;
use Swis\McpClient\Schema\Content\TextContent;

/**
 * DTO for the get prompt result
 *
 * @phpstan-type GetPromptResultData array{description: string|null, messages: array{}, _meta?: array{}}
 */
class GetPromptResult extends BaseResult
{
    /**
     * @param string $requestId The request ID this result is for
     * @param array<TextContent|ImageContent|EmbeddedResource> $messages The message of the prompt
     * @param string|null $description The description of the prompt
     * @param array{}|null $meta Optional metadata
     */
    public function __construct(
        string $requestId,
        protected array $messages,
        protected ?string $description,
        ?array $meta = null
    ) {
        parent::__construct($requestId, $meta);
    }

    /**
     * @return GetPromptResultData
     */
    public function toArray(): array
    {
        $messages = array_map(function ($message) {
            return $message->toArray();
        }, $this->messages);

        return [
            'messages' => $messages,
            'description' => $this->description,
        ];
    }

    /**
     * Get messages
     *
     * @return array<TextContent|ImageContent|EmbeddedResource>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Get prompt
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Create result from array
     *
     * @param GetPromptResultData $data Result data
     * @param string $requestId Request ID
     * @return self
     */
    public static function fromArray(array $data, string $requestId): self
    {
        $messages = array_map(fn (array $messageData) => match ($messageData['type']) {
            'text' => TextContent::fromArray($messageData),
            'image' => ImageContent::fromArray($messageData),
            'resource' => EmbeddedResource::fromArray($messageData),
            default => throw new UnknownContentTypeException('Unknown message type: ' . $messageData['type'])
        }, $data['messages']);

        return new self(
            $requestId,
            $messages,
            $data['description'],
            $data['_meta'] ?? null
        );
    }
}
