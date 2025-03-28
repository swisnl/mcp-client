<?php

namespace Swis\McpClient\Requests;

/**
 * DTO for the resources/list request
 */
class ListResourcesRequest extends BaseRequest
{
    /**
     * @param string|null $cursor Optional pagination cursor
     */
    public function __construct(
        protected ?string $cursor = null
    ) {
        $this->method = 'resources/list';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        $params = [];

        if ($this->cursor !== null) {
            $params['cursor'] = $this->cursor;
        }

        return $params;
    }
}
