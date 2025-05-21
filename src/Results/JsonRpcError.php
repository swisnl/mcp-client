<?php

namespace Swis\McpClient\Results;

/**
 * Represents a JSON-RPC error response
 *
 * @phpstan-import-type Meta from BaseResult
 * @phpstan-type JsonRpcErrorData array{code: int, message: string, data?: array<string, string>, _meta?: Meta}
 */
class JsonRpcError extends BaseResult
{
    /**
     * Constructor
     *
     * @param string $requestId The request ID this error is for
     * @param int $code The error code
     * @param string $message The error message
     * @param array<string, string>|null $data Additional error data
     * @param Meta|null $meta Optional metadata
     */
    public function __construct(
        string $requestId,
        protected int $code,
        protected string $message,
        protected ?array $data = null,
        ?array $meta = null
    ) {
        parent::__construct($requestId, $meta);
    }

    /**
     * Get the error code
     *
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Get the error message
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get additional error data
     *
     * @return array{}|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * Convert the error to an array
     *
     * @return JsonRpcErrorData
     */
    public function toArray(): array
    {
        $data = [
            'code' => $this->code,
            'message' => $this->message,
        ];

        if ($this->data !== null) {
            $data['data'] = $this->data;
        }

        return $data;
    }

    /**
     * Create an error from an array
     *
     * @param JsonRpcErrorData $data Error data
     * @param string $requestId Request ID
     * @return self
     */
    public static function fromArray(array $data, string $requestId): self
    {
        return new self(
            $requestId,
            $data['code'],
            $data['message'],
            $data['data'] ?? null,
            $data['_meta'] ?? null
        );
    }
}
