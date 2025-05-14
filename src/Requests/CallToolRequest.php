<?php

namespace Swis\McpClient\Requests;

/**
 * DTO for the tools/call request
 */
class CallToolRequest extends BaseRequest
{
    /**
     * @param string $name The tool name
     * @param array|null $arguments Optional arguments for the tool
     */
    public function __construct(
        protected string $name,
        protected ?array $arguments = null
    ) {
        $this->method = 'tools/call';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        $params = [
            'name' => $this->name,
        ];

        if ($this->arguments !== null && count($this->arguments) > 0) {
            $params['arguments'] = $this->arguments;
        } else {
            $params['arguments'] = new \stdClass();
        }

        return $params;
    }
}
