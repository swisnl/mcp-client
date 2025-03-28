<?php

namespace Swis\McpClient;

use Swis\McpClient\Results\ResultInterface;

/**
 * Event that contains a response from the MCP server.
 */
class ResponseEvent
{
    /**
     * @var array The response data
     */
    private array $response;

    /**
     * @var ResultInterface|null The result object if available
     */
    private ?ResultInterface $result;

    /**
     * Constructor
     *
     * @param array $response The response data
     * @param ResultInterface|null $result The result object if available
     */
    public function __construct(array $response, ?ResultInterface $result = null)
    {
        $this->response = $response;
        $this->result = $result;
    }

    /**
     * Get the response data
     *
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * Get the result object if available
     *
     * @return ResultInterface|null
     */
    public function getResult(): ?ResultInterface
    {
        return $this->result;
    }

    /**
     * Check if a result object is available
     *
     * @return bool
     */
    public function hasResult(): bool
    {
        return $this->result !== null;
    }
}
