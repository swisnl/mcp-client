<?php

namespace Swis\McpClient\Requests;

/**
 * DTO for the initialized notification
 */
class InitializedNotificationRequest extends BaseRequest
{
    /**
     * @param array|null $_meta Optional metadata
     */
    public function __construct(
        protected ?array $_meta = null
    ) {
        $this->method = 'notifications/initialized';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        $params = [];

        if ($this->_meta !== null) {
            $params['_meta'] = $this->_meta;
        }

        return $params;
    }
}
