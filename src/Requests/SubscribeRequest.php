<?php

namespace Swis\McpClient\Requests;

/**
 * DTO for the resources/subscribe request
 */
class SubscribeRequest extends BaseRequest
{
    /**
     * @param string $uri The resource URI to subscribe to
     */
    public function __construct(
        protected string $uri
    ) {
        $this->method = 'resources/subscribe';
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
