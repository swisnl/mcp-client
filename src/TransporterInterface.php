<?php

namespace Swis\McpClient;

use Psr\EventDispatcher\EventDispatcherInterface;
use Swis\McpClient\Requests\RequestInterface;

/**
 * Interface for MCP transporters that handle communication with an MCP server.
 */
interface TransporterInterface
{
    /**
     * Connect to the MCP server
     */
    public function connect(): void;

    /**
     * Send a request to the MCP server
     *
     * @param RequestInterface $request The request to send
     * @return \React\Promise\PromiseInterface<mixed> A promise that resolves when the request has been sent
     */
    public function sendRequest(RequestInterface $request): \React\Promise\PromiseInterface;

    /**
     * Start listening for responses from the MCP server
     * This should be non-blocking
     *
     * @param EventDispatcherInterface $eventDispatcher Dispatcher for response events
     */
    public function listen(EventDispatcherInterface $eventDispatcher): void;

    /**
     * Disconnect from the MCP server
     */
    public function disconnect(): void;

    /**
     * Check if the transporter is connected
     *
     * @return bool
     */
    public function isConnected(): bool;

    /**
     * Clear the internal request map
     */
    public function clearRequestMap(): void;
}
