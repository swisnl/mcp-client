<?php

namespace Swis\McpClient\Results;

use Swis\McpClient\Exceptions\InvalidResultClassException;
use Swis\McpClient\Requests\CallToolRequest;
use Swis\McpClient\Requests\CompleteRequest;
use Swis\McpClient\Requests\GetPromptRequest;
use Swis\McpClient\Requests\InitializeRequest;
use Swis\McpClient\Requests\ListPromptsRequest;
use Swis\McpClient\Requests\ListResourcesRequest;
use Swis\McpClient\Requests\ListResourceTemplatesRequest;
use Swis\McpClient\Requests\ListToolsRequest;
use Swis\McpClient\Requests\ReadResourceRequest;
use Swis\McpClient\Requests\RequestInterface;

/**
 * Factory to create Result objects from response data
 */
class ResultFactory
{
    /**
     * Map of request methods to result classes
     *
     * @var array<string, string>
     */
    protected static array $requestToResultMap = [
        InitializeRequest::class => InitializeResult::class,
        ListResourcesRequest::class => ListResourcesResult::class,
        ListPromptsRequest::class => ListPromptsResult::class,
        ListToolsRequest::class => ListToolsResult::class,
        ReadResourceRequest::class => ReadResourceResult::class,
        CallToolRequest::class => CallToolResult::class,
        CompleteRequest::class => CompleteResult::class,
        ListResourceTemplatesRequest::class => ListResourceTemplatesResult::class,
        GetPromptRequest::class => GetPromptResult::class,
    ];

    /**
     * Create a Result object from response data
     *
     * @param array $responseData The response data
     * @param array<string, RequestInterface> $requestMap Map of request IDs to request objects
     * @return ResultInterface|null The result object, or null if no matching result class is found
     */
    public static function createFromResponse(array $responseData, array $requestMap): ?ResultInterface
    {
        // Check if response has an ID
        if (! isset($responseData['id'])) {
            return null;
        }

        if (isset($responseData['error']) && is_array($responseData['error'])) {
            return JsonRpcError::fromArray($responseData['error'], $responseData['id']);
        }

        if (! isset($responseData['result'])) {
            return null;
        }

        $requestId = $responseData['id'];
        $resultData = $responseData['result'];

        // Check if we have a matching request
        if (! isset($requestMap[$requestId])) {
            return null;
        }

        $request = get_class($requestMap[$requestId]);

        // Check if we have a result class for this method
        if (! isset(self::$requestToResultMap[$request])) {
            return null;
        }

        $resultClass = self::$requestToResultMap[$request];
        if (! is_subclass_of($resultClass, ResultInterface::class)) {
            throw new InvalidResultClassException("Result class $resultClass does not implement ResultInterface");
        }

        return $resultClass::fromArray($resultData, $requestId);
    }
}
