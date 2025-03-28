<?php

namespace Swis\McpClient\Requests;

/**
 * Base class for all request DTO objects
 */
abstract class BaseRequest implements RequestInterface
{
    /**
     * The method name for the request
     *
     * @var string
     */
    protected string $method;

    /**
     * The id for the request
     *
     * @var string
     */
    protected string $id;

    /**
     * Convert the DTO to an array structure
     *
     * @return array
     */
    abstract public function toArray(): array;

    /**
     * Get the method name for this request
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Set the id for this request
     *
     * @param string $id
     * @return $this
     */
    public function withId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id for this request
     *
     * @return string
     */
    public function getId(): string
    {
        if (! isset($this->id)) {
            $this->id = $this->generateRequestId();
        }

        return $this->id;
    }

    /**
     * Implement JsonSerializable interface
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        $data = [
            'id' => $this->getId(),
            'jsonrpc' => '2.0',
            'method' => $this->getMethod(),
        ];

        $params = $this->toArray();
        if (! empty($params)) {
            $data['params'] = $params;
        }

        return $data;
    }

    /**
     * Generate a unique request ID
     *
     * @return string
     */
    protected function generateRequestId(): string
    {
        return bin2hex(random_bytes(16));
    }
}
