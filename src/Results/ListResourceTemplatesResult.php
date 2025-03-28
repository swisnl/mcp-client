<?php

namespace Swis\McpClient\Results;

use Swis\McpClient\Schema\ResourceTemplate;

/**
 * DTO for the list resource templates result
 *
 * @phpstan-type ListResourceTemplatesResultData array{templates: array<array{templateId: string, name: string, description: string, type: string, uri: string, tags: array<string>}>, nextCursor?: string|null, _meta?: array{}}
 */
class ListResourceTemplatesResult extends BaseResult
{
    /**
     * @param string $requestId The request ID this result is for
     * @param ResourceTemplate[] $templates The list of resource templates
     * @param string|null $nextCursor Optional cursor for pagination
     * @param array{}|null $meta Optional metadata
     */
    public function __construct(
        string $requestId,
        protected array $templates,
        protected ?string $nextCursor = null,
        ?array $meta = null
    ) {
        parent::__construct($requestId, $meta);
    }

    /**
     * @return ListResourceTemplatesResultData
     */
    public function toArray(): array
    {
        $templates = array_map(function (ResourceTemplate $template) {
            return $template->toArray();
        }, $this->templates);

        $data = [
            'templates' => $templates,
        ];

        if ($this->nextCursor !== null) {
            $data['nextCursor'] = $this->nextCursor;
        }

        return $data;
    }

    /**
     * Get templates
     *
     * @return ResourceTemplate[]
     */
    public function getTemplates(): array
    {
        return $this->templates;
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
     * @param ListResourceTemplatesResultData $data Result data
     * @param string $requestId Request ID
     * @return self
     */
    public static function fromArray(array $data, string $requestId): self
    {
        $templates = array_map(function (array $templateData) {
            return ResourceTemplate::fromArray($templateData);
        }, $data['templates']);

        return new self(
            $requestId,
            $templates,
            $data['nextCursor'] ?? null,
            $data['_meta'] ?? null
        );
    }
}
