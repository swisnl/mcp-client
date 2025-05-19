<?php

namespace Swis\McpClient\Results;

use Swis\McpClient\Schema\PromptMessage;

/**
 * DTO for the get prompt result
 *
 * @phpstan-import-type PromptMessageData from \Swis\McpClient\Schema\PromptMessage
 * @phpstan-type GetPromptResultData array{description: string|null, messages: array<PromptMessageData>, _meta?: array{}}
 */
class GetPromptResult extends BaseResult
{
    /**
     * @param string $requestId The request ID this result is for
     * @param array<PromptMessage> $messages The messages of the prompt
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
     * @return array<PromptMessage>
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
        $messages = array_map(fn (array $messageData) => PromptMessage::fromArray($messageData), $data['messages']);

        return new self(
            $requestId,
            $messages,
            $data['description'] ?? null,
            $data['_meta'] ?? null
        );
    }
}
