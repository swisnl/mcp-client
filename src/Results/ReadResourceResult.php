<?php

namespace Swis\McpClient\Results;

use Swis\McpClient\Schema\Resource\BlobResourceContents;
use Swis\McpClient\Schema\Resource\TextResourceContents;

/**
 * DTO for the read resource result
 *
 * @phpstan-import-type Meta from BaseResult
 * @phpstan-type ReadResourceResultData array{contents: array<array{blob: string, uri: string, mimeType?: string}|array{text: string, uri: string, mimeType?: string}>, _meta?: Meta}
 */
class ReadResourceResult extends BaseResult
{
    /**
     * @param string $requestId The request ID this result is for
     * @param array<TextResourceContents|BlobResourceContents> $contents The resource contents
     * @param Meta|null $meta Optional metadata
     */
    public function __construct(
        string $requestId,
        protected array $contents,
        ?array $meta = null
    ) {
        parent::__construct($requestId, $meta);
    }

    /**
     * @return ReadResourceResultData
     */
    public function toArray(): array
    {
        return [
            'contents' => array_map(fn ($content) => $content->toArray(), $this->contents),
        ];
    }

    /**
     * Get resource contents
     *
     * @return array<TextResourceContents|BlobResourceContents>
     */
    public function getContents(): array
    {
        return $this->contents;
    }

    /**
     * Create result from array
     *
     * @param ReadResourceResultData $data Result data
     * @param string $requestId Request ID
     * @return self
     */
    public static function fromArray(array $data, string $requestId): self
    {
        $contents = [];

        foreach ($data['contents'] as $content) {
            if (isset($content['text'])) {
                $contents[] = TextResourceContents::fromArray($content);
            } elseif (isset($content['blob'])) {
                $contents[] = BlobResourceContents::fromArray($content);
            } else {
                throw new \Swis\McpClient\Exceptions\UnknownResourceContentTypeException('Unknown resource content type');
            }
        }

        return new self(
            $requestId,
            $contents,
            $data['_meta'] ?? null
        );
    }
}
