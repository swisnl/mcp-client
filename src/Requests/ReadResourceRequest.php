<?php

namespace Swis\McpClient\Requests;

/**
 * DTO for the resources/read request
 */
class ReadResourceRequest extends BaseRequest
{
    /**
     * @param string $uri The resource URI to read
     */
    public function __construct(
        protected string $uri
    ) {
        $this->method = 'resources/read';
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
