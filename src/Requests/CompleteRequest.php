<?php

namespace Swis\McpClient\Requests;

/**
 * DTO for the completion/complete request
 */
class CompleteRequest extends BaseRequest
{
    /**
     * @param array $argument The argument with name and value
     * @param array $reference The reference prompt or resource
     */
    public function __construct(
        protected array $argument,
        protected array $reference
    ) {
        $this->method = 'completion/complete';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'argument' => $this->argument,
            'reference' => $this->reference,
        ];
    }
}
