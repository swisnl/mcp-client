<?php

namespace Swis\McpClient\Results;

use Swis\McpClient\Schema\Resource;

/**
 * DTO for the list resources result
 *
 * @phpstan-import-type Meta from BaseResult
 * @phpstan-type ListResourcesResultData array{resources: array<array{resourceId: string, name: string, type: string, uri: string, tags: array<string>}>, nextCursor?: string|null, _meta?: Meta}
 */
class ListResourcesResult extends BaseResult
{
    /**
     * @param string $requestId The request ID this result is for
     * @param Resource[] $resources The list of resources
     * @param string|null $nextCursor Optional cursor for pagination
     * @param Meta|null $meta Optional metadata
     */
    public function __construct(
        string $requestId,
        protected array $resources,
        protected ?string $nextCursor = null,
        ?array $meta = null
    ) {
        parent::__construct($requestId, $meta);
    }

    /**
     * @return ListResourcesResultData
     */
    public function toArray(): array
    {
        $resources = array_map(function (Resource $resource) {
            return $resource->toArray();
        }, $this->resources);

        $data = [
            'resources' => $resources,
        ];

        if ($this->nextCursor !== null) {
            $data['nextCursor'] = $this->nextCursor;
        }

        return $data;
    }

    /**
     * Get resources
     *
     * @return Resource[]
     */
    public function getResources(): array
    {
        return $this->resources;
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
     * @param ListResourcesResultData $data Result data
     * @param string $requestId Request ID
     * @return self
     */
    public static function fromArray(array $data, string $requestId): self
    {
        $resources = array_map(function (array $resourceData) {
            return Resource::fromArray($resourceData);
        }, $data['resources']);

        return new self(
            $requestId,
            $resources,
            $data['nextCursor'] ?? null,
            $data['_meta'] ?? null
        );
    }
}
