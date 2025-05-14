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
     * This method will internally await the connection and initialization,
     * blocking until the connection is fully established
     *
     * @param EventDispatcherInterface $eventDispatcher Dispatcher for connection events
     * @param $capabilities array<string, bool|float|int|string> Client capabilities
     * @param $clientInfo array<string, string> Client information
     * @param $protocolVersion string Protocol version (e.g., "2025-03-26")
     *
     * @return array<string, scalar> Server information
     */
    public function initializeConnection(EventDispatcherInterface $eventDispatcher, array $capabilities, array $clientInfo, string $protocolVersion): array;

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
