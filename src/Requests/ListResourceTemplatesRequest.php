<?php

namespace Swis\McpClient\Requests;

/**
 * DTO for the resources/templates/list request
 */
class ListResourceTemplatesRequest extends BaseRequest
{
    /**
     * @param string|null $cursor Optional pagination cursor
     */
    public function __construct(
        protected ?string $cursor = null
    ) {
        $this->method = 'resources/templates/list';
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
