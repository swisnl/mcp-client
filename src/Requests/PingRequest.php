<?php

namespace Swis\McpClient\Requests;

/**
 * DTO for the ping request
 */
class PingRequest extends BaseRequest
{
    /**
     * @param string|null $progressToken Optional progress token
     */
    public function __construct(
        protected ?string $progressToken = null
    ) {
        $this->method = 'ping';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        $params = [];

        if ($this->progressToken !== null) {
            $params['progressToken'] = $this->progressToken;
        }

        return $params;
    }
}
