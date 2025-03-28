<?php

namespace Swis\McpClient\Requests;

/**
 * DTO for the logging/setLevel request
 */
class SetLevelRequest extends BaseRequest
{
    /**
     * @param string $level The logging level to set
     */
    public function __construct(
        protected string $level
    ) {
        $this->method = 'logging/setLevel';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'level' => $this->level,
        ];
    }
}
