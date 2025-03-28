<?php

namespace Swis\McpClient\Requests;

/**
 * DTO for the prompts/get request
 */
class GetPromptRequest extends BaseRequest
{
    /**
     * @param string $name The prompt name
     * @param array|null $arguments Optional arguments for prompt templating
     */
    public function __construct(
        protected string $name,
        protected ?array $arguments = null
    ) {
        $this->method = 'prompts/get';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        $params = [
            'name' => $this->name,
        ];

        if ($this->arguments !== null) {
            $params['arguments'] = $this->arguments;
        }

        return $params;
    }
}
