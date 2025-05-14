<?php

namespace Swis\McpClient\Results;

use Swis\McpClient\Schema\Tool;

/**
 * DTO for the list tools result
 *
 * @phpstan-type ListToolsResultData array{tools: array{name: string, inputSchema?: array{properties: array{type: string, mixed}, required: array<string>, type: string}|array{}, description?: string, annotations?: array<string, mixed>}, nextCursor?: string|null, _meta?: array{}}
 */
class ListToolsResult extends BaseResult
{
    /**
     * @param string $requestId The request ID this result is for
     * @param Tool[] $tools The list of tools
     * @param string|null $nextCursor Optional cursor for pagination
     * @param array{}|null $meta Optional metadata
     */
    public function __construct(
        string $requestId,
        protected array $tools,
        protected ?string $nextCursor = null,
        ?array $meta = null
    ) {
        parent::__construct($requestId, $meta);
    }

    /**
     * @return ListToolsResultData
     */
    public function toArray(): array
    {
        $tools = array_map(function (Tool $tool) {
            return $tool->toArray();
        }, $this->tools);

        $data = [
            'tools' => $tools,
        ];

        if ($this->nextCursor !== null) {
            $data['nextCursor'] = $this->nextCursor;
        }

        return $data;
    }

    /**
     * Get tools
     *
     * @return Tool[]
     */
    public function getTools(): array
    {
        return $this->tools;
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
     * @param ListToolsResultData $data Result data
     * @param string $requestId Request ID
     * @return self
     */
    public static function fromArray(array $data, string $requestId): self
    {
        $tools = array_map(function (array $toolData) {
            return Tool::fromArray($toolData);
        }, $data['tools']);

        return new self(
            $requestId,
            $tools,
            $data['nextCursor'] ?? null,
            $data['_meta'] ?? null
        );
    }
}
