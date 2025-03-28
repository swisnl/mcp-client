<?php

namespace Swis\McpClient\Results;

/**
 * Base class for all result DTO objects
 */
abstract class BaseResult implements ResultInterface
{
    /**
     * The request ID associated with this result
     *
     * @var string
     */
    protected string $requestId;

    /**
     * Optional metadata
     *
     * @var array{}|null
     */
    protected ?array $meta = null;

    /**
     * Constructor
     *
     * @param string $requestId The request ID this result is for
     * @param array{}|null $meta Optional metadata
     */
    public function __construct(string $requestId, ?array $meta = null)
    {
        $this->requestId = $requestId;
        $this->meta = $meta;
    }

    /**
     * Get the request ID this result is for
     *
     * @return string
     */
    public function getRequestId(): string
    {
        return $this->requestId;
    }

    /**
     * Get metadata associated with this result
     *
     * @return array{}|null
     */
    public function getMeta(): ?array
    {
        return $this->meta;
    }

    /**
     * Convert the result to an array
     *
     * @return array
     */
    abstract public function toArray(): array;

    /**
     * Implement JsonSerializable interface
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        $data = $this->toArray();

        if ($this->meta !== null) {
            $data['_meta'] = $this->meta;
        }

        return $data;
    }

    /**
     * Create a result from an array
     *
     * @param array $data Result data
     * @param string $requestId Request ID
     * @return self
     */
    public static function fromArray(array $data, string $requestId): self
    {
        return new static($requestId, $data['_meta'] ?? null);
    }
}
