<?php

namespace Swis\McpClient\Results;

/**
 * DTO for the complete result
 *
 * @phpstan-import-type Meta from BaseResult
 * @phpstan-type CompletionData array{hasMore?: bool, total?: int, values?: array<string>}
 * @phpstan-type CompleteResultData array{completion: CompletionData, _meta?: Meta}
 */
class CompleteResult extends BaseResult
{
    /**
     * @param string $requestId The request ID this result is for
     * @param CompletionData $completion The completion results
     * @param Meta|null $meta Optional metadata
     */
    public function __construct(
        string $requestId,
        protected array $completion,
        ?array $meta = null
    ) {
        parent::__construct($requestId, $meta);
    }

    /**
     * @return CompleteResultData
     */
    public function toArray(): array
    {
        return [
            'completion' => $this->completion,
        ];
    }

    /**
     * Get completion
     *
     * @return array
     */
    public function getCompletion(): array
    {
        return $this->completion;
    }

    /**
     * Get completion values
     *
     * @return array
     */
    public function getValues(): array
    {
        return $this->completion['values'] ?? [];
    }

    /**
     * Get total completions
     *
     * @return int
     */
    public function getTotal(): int
    {
        return $this->completion['total'] ?? count($this->getValues());
    }

    /**
     * Check if there are more completions
     *
     * @return bool
     */
    public function hasMore(): bool
    {
        return $this->completion['hasMore'] ?? false;
    }

    /**
     * Create result from array
     *
     * @param CompleteResultData $data Result data
     * @param string $requestId Request ID
     * @return self
     */
    public static function fromArray(array $data, string $requestId): self
    {
        return new self(
            $requestId,
            $data['completion'],
            $data['_meta'] ?? null
        );
    }
}
