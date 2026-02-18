<?php

namespace Swis\McpClient;

use function React\Async\await;

use React\Promise\PromiseInterface;
use Swis\McpClient\Exceptions\ConnectionAbortedEarlyException;
use Swis\McpClient\Requests\CallToolRequest;
use Swis\McpClient\Requests\CompleteRequest;
use Swis\McpClient\Requests\GetPromptRequest;
use Swis\McpClient\Requests\ListPromptsRequest;
use Swis\McpClient\Requests\ListResourcesRequest;
use Swis\McpClient\Requests\ListResourceTemplatesRequest;
use Swis\McpClient\Requests\ListToolsRequest;
use Swis\McpClient\Requests\PingRequest;
use Swis\McpClient\Requests\ReadResourceRequest;
use Swis\McpClient\Requests\SetLevelRequest;
use Swis\McpClient\Requests\SubscribeRequest;
use Swis\McpClient\Requests\UnsubscribeRequest;
use Swis\McpClient\Results\CallToolResult;
use Swis\McpClient\Results\CompleteResult;
use Swis\McpClient\Results\GetPromptResult;
use Swis\McpClient\Results\JsonRpcError;
use Swis\McpClient\Results\ListPromptsResult;
use Swis\McpClient\Results\ListResourcesResult;
use Swis\McpClient\Results\ListResourceTemplatesResult;
use Swis\McpClient\Results\ListToolsResult;
use Swis\McpClient\Results\ReadResourceResult;
use Swis\McpClient\Transporters\StdioTransporter;

/**
 * Trait with helper methods for all request types.
 *
 * This trait should be used with the Client class.
 */
trait ClientRequestTrait
{
    /**
     * Complete a text with AI assistance
     *
     * @param CompleteRequest $request The complete request
     * @return CompleteResult|JsonRpcError
     */
    public function complete(CompleteRequest $request): CompleteResult|JsonRpcError
    {
        return $this->await($this->sendRequestAsync($request));
    }

    /**
     * Call a tool
     *
     * @param CallToolRequest $request The call tool request
     * @return CallToolResult|JsonRpcError
     */
    public function callTool(CallToolRequest $request): CallToolResult|JsonRpcError
    {
        return $this->await($this->sendRequestAsync($request));
    }

    /**
     * Get a prompt by ID
     *
     * @param GetPromptRequest $request The get prompt request
     * @return GetPromptResult|JsonRpcError
     */
    public function getPrompt(GetPromptRequest $request): GetPromptResult|JsonRpcError
    {
        return $this->await($this->sendRequestAsync($request));
    }

    /**
     * List all available prompts
     *
     * @param ListPromptsRequest|null $request The list prompts request
     * @return ListPromptsResult|JsonRpcError
     */
    public function listPrompts(?ListPromptsRequest $request = null): ListPromptsResult|JsonRpcError
    {
        $request = $request ?? new ListPromptsRequest();

        return $this->await($this->sendRequestAsync($request));
    }

    /**
     * List all available resources
     *
     * @param ListResourcesRequest|null $request The list resources request
     * @return ListResourcesResult|JsonRpcError
     */
    public function listResources(?ListResourcesRequest $request = null): ListResourcesResult|JsonRpcError
    {
        $request = $request ?? new ListResourcesRequest();

        return $this->await($this->sendRequestAsync($request));
    }

    /**
     * List all available resource templates
     *
     * @param ListResourceTemplatesRequest|null $request The list resource templates request
     * @return ListResourceTemplatesResult|JsonRpcError
     */
    public function listResourceTemplates(?ListResourceTemplatesRequest $request = null): ListResourceTemplatesResult|JsonRpcError
    {
        $request = $request ?? new ListResourceTemplatesRequest();

        return $this->await($this->sendRequestAsync($request));
    }

    /**
     * List all available tools
     *
     * @param ListToolsRequest|null $request The list tools request
     * @return ListToolsResult|JsonRpcError
     */
    public function listTools(?ListToolsRequest $request = null): ListToolsResult|JsonRpcError
    {
        $request = $request ?? new ListToolsRequest();

        return $this->await($this->sendRequestAsync($request));
    }

    /**
     * Ping the server
     *
     * @param PingRequest|null $request The ping request
     * @return true|JsonRpcError True if ping was successful
     */
    public function ping(?PingRequest $request = null): bool|JsonRpcError
    {
        $request = $request ?? new PingRequest();
        $result = $this->await($this->sendRequestAsync($request));

        if ($result instanceof JsonRpcError) {
            return $result;
        }

        return (bool) $result;
    }

    /**
     * Read a resource
     *
     * @param ReadResourceRequest $request The read resource request
     * @return ReadResourceResult|JsonRpcError
     */
    public function readResource(ReadResourceRequest $request): ReadResourceResult|JsonRpcError
    {
        return $this->await($this->sendRequestAsync($request));
    }

    /**
     * Set logging level
     *
     * @param SetLevelRequest $request The set level request
     * @return void
     */
    public function setLevel(SetLevelRequest $request): void
    {
        $this->await($this->sendRequestAsync($request));
    }

    /**
     * Subscribe to events
     *
     * @param SubscribeRequest $request The subscribe request
     * @return void
     */
    public function subscribe(SubscribeRequest $request): void
    {
        $this->await($this->sendRequestAsync($request));
    }

    /**
     * Unsubscribe from events
     *
     * @param UnsubscribeRequest $request The unsubscribe request
     * @return void
     */
    public function unsubscribe(UnsubscribeRequest $request): void
    {
        $this->await($this->sendRequestAsync($request));
    }

    protected function await(PromiseInterface $promise): mixed
    {
        try {
            return await($promise);
        } catch (\AssertionError $e) {
            if (str_contains($e->getMessage(), 'is_callable($ret)')) {
                if ($this->transporter instanceof StdioTransporter) {
                    $errorBag = $this->transporter->getErrorBag();

                    $error = 'Connection aborted early. Mostly this happens if the process is killed before the connection is established. Check for any errors in your command string.';
                    if (count($errorBag) > 0) {
                        $error .= "\n\nLatest errors:\n" . implode("\n", array_slice($errorBag, -3));
                    }

                    $connectionAbortedEarlyException = new ConnectionAbortedEarlyException($error, 0, $e);
                    $connectionAbortedEarlyException->setErrorBag($errorBag);

                    throw $connectionAbortedEarlyException;
                }

                throw new ConnectionAbortedEarlyException('Connection aborted early. Check your MCP server URL and settings.', 0, $e);
            }

            throw $e;
        }
    }
}
