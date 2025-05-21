<?php

namespace Swis\McpClient\Results;

/**
 * DTO for the list prompts result
 *
 * @phpstan-import-type Meta from BaseResult
 * @phpstan-type PromptArgument array{name: string, description?: string, required?: bool}
 * @phpstan-type Prompt array{name: string, description?: string, arguments?: array<PromptArgument>}
 * @phpstan-type ListPromptsResultData array{prompts: array<Prompt>, nextCursor?: string, _meta?: Meta}
 */
class ListPromptsResult extends BaseResult
{
    /**
     * @param string $requestId The request ID this result is for
     * @param array<Prompt> $prompts The list of prompts
     * @param string|null $nextCursor Optional cursor for pagination
     * @param Meta|null $meta Optional metadata
     */
    public function __construct(
        string $requestId,
        protected array $prompts,
        protected ?string $nextCursor = null,
        ?array $meta = null
    ) {
        parent::__construct($requestId, $meta);
    }

    /**
     * @return ListPromptsResultData
     */
    public function toArray(): array
    {
        $data = [
            'prompts' => $this->prompts,
        ];

        if ($this->nextCursor !== null) {
            $data['nextCursor'] = $this->nextCursor;
        }

        return $data;
    }

    /**
     * Get prompts
     *
     * @return array<Prompt>
     */
    public function getPrompts(): array
    {
        return $this->prompts;
    }

    /**
     * Get next cursor
     *
     * @return string|null
     */
    public function getNextCursor(): ?string
    {
        return $this->nextCursor;
    }

    /**
     * Create result from array
     *
     * @param ListPromptsResultData $data Result data
     * @param string $requestId Request ID
     * @return self
     */
    public static function fromArray(array $data, string $requestId): self
    {
        return new self(
            $requestId,
            $data['prompts'],
            $data['nextCursor'] ?? null,
            $data['_meta'] ?? null
        );
    }
}
