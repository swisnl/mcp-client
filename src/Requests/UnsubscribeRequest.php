<?php

namespace Swis\McpClient\Requests;

/**
 * DTO for the resources/unsubscribe request
 */
class UnsubscribeRequest extends BaseRequest
{
    /**
     * @param string $uri The resource URI to unsubscribe from
     */
    public function __construct(
        protected string $uri
    ) {
        $this->method = 'resources/unsubscribe';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'uri' => $this->uri,
        ];
    }
}
