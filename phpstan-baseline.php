<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\AbstractTransporter\\:\\:dispatchResponse\\(\\) has parameter \\$response with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/AbstractTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\AbstractTransporter\\:\\:initializeConnection\\(\\) has parameter \\$capabilities with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/AbstractTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\AbstractTransporter\\:\\:initializeConnection\\(\\) has parameter \\$clientInfo with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/AbstractTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\AbstractTransporter\\:\\:initializeConnection\\(\\) should return array\\<string, bool\\|float\\|int\\|string\\> but returns array\\<mixed, mixed\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/AbstractTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe method call on non\\-nullable type React\\\\EventLoop\\\\LoopInterface\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/src/AbstractTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Client\\:\\:await\\(\\) has parameter \\$deferred with generic class React\\\\Promise\\\\Deferred but does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Client.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Client\\:\\:callTool\\(\\) should return Swis\\\\McpClient\\\\Results\\\\CallToolResult\\|Swis\\\\McpClient\\\\Results\\\\JsonRpcError but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Client.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Client\\:\\:complete\\(\\) should return Swis\\\\McpClient\\\\Results\\\\CompleteResult\\|Swis\\\\McpClient\\\\Results\\\\JsonRpcError but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Client.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Client\\:\\:getPrompt\\(\\) should return Swis\\\\McpClient\\\\Results\\\\GetPromptResult\\|Swis\\\\McpClient\\\\Results\\\\JsonRpcError but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Client.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Client\\:\\:listPrompts\\(\\) should return Swis\\\\McpClient\\\\Results\\\\JsonRpcError\\|Swis\\\\McpClient\\\\Results\\\\ListPromptsResult but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Client.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Client\\:\\:listResourceTemplates\\(\\) should return Swis\\\\McpClient\\\\Results\\\\JsonRpcError\\|Swis\\\\McpClient\\\\Results\\\\ListResourceTemplatesResult but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Client.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Client\\:\\:listResources\\(\\) should return Swis\\\\McpClient\\\\Results\\\\JsonRpcError\\|Swis\\\\McpClient\\\\Results\\\\ListResourcesResult but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Client.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Client\\:\\:listTools\\(\\) should return Swis\\\\McpClient\\\\Results\\\\JsonRpcError\\|Swis\\\\McpClient\\\\Results\\\\ListToolsResult but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Client.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Client\\:\\:ping\\(\\) should return Swis\\\\McpClient\\\\Results\\\\JsonRpcError\\|true but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Client.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Client\\:\\:readResource\\(\\) should return Swis\\\\McpClient\\\\Results\\\\JsonRpcError\\|Swis\\\\McpClient\\\\Results\\\\ReadResourceResult but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Client.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Client\\:\\:sendRequest\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Client.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Client\\:\\:sendRequest\\(\\) should return React\\\\Promise\\\\PromiseInterface\\<array\\|bool\\|Exception\\|float\\|int\\|string\\> but returns React\\\\Promise\\\\PromiseInterface\\<mixed\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Client.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Client\\:\\:sendRequestAsync\\(\\) should return React\\\\Promise\\\\PromiseInterface\\<Swis\\\\McpClient\\\\Results\\\\ResultInterface\\> but returns React\\\\Promise\\\\PromiseInterface\\<mixed\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Client.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$inputStream of class Swis\\\\McpClient\\\\Transporters\\\\StdioTransporter constructor expects resource\\|string\\|null, resource\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Client.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$outputStream of class Swis\\\\McpClient\\\\Transporters\\\\StdioTransporter constructor expects resource\\|string\\|null, resource\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Client.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\$env of static method Swis\\\\McpClient\\\\Factories\\\\ProcessFactory\\:\\:createTransporterForProcess\\(\\) expects array\\<string, string\\>, array\\<string, bool\\|float\\|int\\|string\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Client.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\BaseRequest\\:\\:jsonSerialize\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/BaseRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\BaseRequest\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/BaseRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\CallToolRequest\\:\\:__construct\\(\\) has parameter \\$arguments with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/CallToolRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\CallToolRequest\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/CallToolRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\CompleteRequest\\:\\:__construct\\(\\) has parameter \\$argument with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/CompleteRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\CompleteRequest\\:\\:__construct\\(\\) has parameter \\$reference with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/CompleteRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\CompleteRequest\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/CompleteRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\GetPromptRequest\\:\\:__construct\\(\\) has parameter \\$arguments with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/GetPromptRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\GetPromptRequest\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/GetPromptRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\InitializeRequest\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/InitializeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Swis\\\\McpClient\\\\Requests\\\\InitializeRequest\\:\\:\\$capabilities \\(array\\<string, array\\<string, bool\\|float\\|int\\|string\\>\\>\\) does not accept array\\<string, array\\<string, bool\\|float\\|int\\|string\\>\\|stdClass\\>\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/InitializeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\InitializedNotificationRequest\\:\\:__construct\\(\\) has parameter \\$_meta with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/InitializedNotificationRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\InitializedNotificationRequest\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/InitializedNotificationRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\ListPromptsRequest\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/ListPromptsRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\ListResourceTemplatesRequest\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/ListResourceTemplatesRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\ListResourcesRequest\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/ListResourcesRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\ListToolsRequest\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/ListToolsRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\PingRequest\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/PingRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\ReadResourceRequest\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/ReadResourceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\SetLevelRequest\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/SetLevelRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\SubscribeRequest\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/SubscribeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Requests\\\\UnsubscribeRequest\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Requests/UnsubscribeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\ResponseEvent\\:\\:__construct\\(\\) has parameter \\$response with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/ResponseEvent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\ResponseEvent\\:\\:getResponse\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/ResponseEvent.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Swis\\\\McpClient\\\\ResponseEvent\\:\\:\\$response type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/ResponseEvent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Results\\\\BaseResult\\:\\:fromArray\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/BaseResult.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Results\\\\BaseResult\\:\\:jsonSerialize\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/BaseResult.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Results\\\\BaseResult\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/BaseResult.php',
];
$ignoreErrors[] = [
	'message' => '#^Unsafe usage of new static\\(\\)\\.$#',
	'identifier' => 'new.static',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/BaseResult.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Results\\\\CallToolResult\\:\\:toArray\\(\\) should return array\\{content\\: array\\<Swis\\\\McpClient\\\\Schema\\\\Content\\\\EmbeddedResource\\|Swis\\\\McpClient\\\\Schema\\\\Content\\\\ImageContent\\|Swis\\\\McpClient\\\\Schema\\\\Content\\\\TextContent\\>, isError\\?\\: bool, _meta\\?\\: array\\<string, mixed\\>\\} but returns array\\{content\\: array\\<array\\{type\\: \'image\', data\\: string, mimeType\\: string, annotations\\?\\: array\\{audience\\?\\: array\\<\'assistant\'\\|\'user\'\\>, priority\\?\\: float\\}\\}\\|array\\{type\\: \'resource\', resource\\: array\\{blob\\: string, uri\\: string, mimeType\\?\\: string\\}\\|array\\{text\\: string, uri\\: string, mimeType\\?\\: string\\}, annotations\\?\\: array\\{audience\\?\\: array\\<\'assistant\'\\|\'user\'\\>, priority\\?\\: float\\}\\}\\|array\\{type\\: \'text\', text\\: string, annotations\\?\\: array\\{audience\\?\\: array\\<\'assistant\'\\|\'user\'\\>, priority\\?\\: float\\}\\}\\>, isError\\?\\: true\\}\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/CallToolResult.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(Swis\\\\McpClient\\\\Schema\\\\Content\\\\EmbeddedResource\\|Swis\\\\McpClient\\\\Schema\\\\Content\\\\ImageContent\\|Swis\\\\McpClient\\\\Schema\\\\Content\\\\TextContent\\)\\: mixed\\)\\|null, Closure\\(array\\)\\: \\(Swis\\\\McpClient\\\\Schema\\\\Content\\\\EmbeddedResource\\|Swis\\\\McpClient\\\\Schema\\\\Content\\\\ImageContent\\|Swis\\\\McpClient\\\\Schema\\\\Content\\\\TextContent\\) given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/CallToolResult.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$data of static method Swis\\\\McpClient\\\\Schema\\\\Content\\\\EmbeddedResource\\:\\:fromArray\\(\\) expects array\\{type\\: \'resource\', resource\\: array\\{blob\\: string, uri\\: string, mimeType\\?\\: string\\}\\|array\\{text\\: string, uri\\: string, mimeType\\?\\: string\\}, annotations\\?\\: array\\{audience\\?\\: array\\<\'assistant\'\\|\'user\'\\>, priority\\?\\: float\\}\\}, non\\-empty\\-array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/CallToolResult.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$data of static method Swis\\\\McpClient\\\\Schema\\\\Content\\\\ImageContent\\:\\:fromArray\\(\\) expects array\\{type\\: \'image\', data\\: string, mimeType\\: string, annotations\\?\\: array\\{audience\\?\\: array\\<\'assistant\'\\|\'user\'\\>, priority\\?\\: float\\}\\}, non\\-empty\\-array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/CallToolResult.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$data of static method Swis\\\\McpClient\\\\Schema\\\\Content\\\\TextContent\\:\\:fromArray\\(\\) expects array\\{type\\: \'text\', text\\: string, annotations\\?\\: array\\{audience\\?\\: array\\<\'assistant\'\\|\'user\'\\>, priority\\?\\: float\\}\\}, non\\-empty\\-array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/CallToolResult.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Results\\\\CompleteResult\\:\\:getCompletion\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/CompleteResult.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Results\\\\CompleteResult\\:\\:getValues\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/CompleteResult.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Results\\\\ListResourcesResult\\:\\:toArray\\(\\) should return array\\{resources\\: array\\<array\\{resourceId\\: string, name\\: string, type\\: string, uri\\: string, tags\\: array\\<string\\>\\}\\>, nextCursor\\?\\: string\\|null, _meta\\?\\: array\\<string, mixed\\>\\} but returns array\\{resources\\: array\\<array\\{name\\: string, uri\\: string, description\\?\\: string, mimeType\\?\\: string, size\\?\\: int, annotations\\?\\: array\\{audience\\?\\: array\\<\'assistant\'\\|\'user\'\\>, priority\\?\\: float\\}\\}\\>, nextCursor\\?\\: string\\}\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/ListResourcesResult.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Results\\\\ListToolsResult\\:\\:toArray\\(\\) should return array\\{tools\\: array\\{name\\: string, inputSchema\\?\\: array\\{\\}\\|array\\{properties\\: array\\{type\\: string, 0\\: mixed\\}, required\\: array\\<string\\>, type\\: string\\}, description\\?\\: string, annotations\\?\\: array\\<string, mixed\\>\\}, nextCursor\\?\\: string\\|null, _meta\\?\\: array\\<string, mixed\\>\\} but returns array\\{tools\\: array\\<array\\{name\\: string, inputSchema\\?\\: array\\{\\}\\|array\\{properties\\: array\\{type\\: string, 0\\: mixed\\}, required\\: array\\<string\\>, type\\: string\\}, description\\?\\: string, annotations\\?\\: array\\<string, mixed\\>\\}\\>, nextCursor\\?\\: string\\}\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/ListToolsResult.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(array\\<string, mixed\\>\\|string\\)\\: mixed\\)\\|null, Closure\\(array\\)\\: Swis\\\\McpClient\\\\Schema\\\\Tool given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/ListToolsResult.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$data of static method Swis\\\\McpClient\\\\Schema\\\\Tool\\:\\:fromArray\\(\\) expects array\\{name\\: string, inputSchema\\?\\: array\\{\\}\\|array\\{properties\\: array\\{type\\: string, 0\\: mixed\\}, required\\: array\\<string\\>, type\\: string\\}, description\\?\\: string, annotations\\?\\: array\\<string, mixed\\>\\}, array\\<string, mixed\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/ListToolsResult.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Results\\\\ResultFactory\\:\\:createFromResponse\\(\\) has parameter \\$responseData with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/ResultFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$data of static method Swis\\\\McpClient\\\\Results\\\\JsonRpcError\\:\\:fromArray\\(\\) expects array\\{code\\: int, message\\: string, data\\?\\: array\\<string, string\\>, _meta\\?\\: array\\<string, mixed\\>\\}, array\\<mixed, mixed\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/ResultFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Results\\\\ResultInterface\\:\\:fromArray\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/ResultInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Results\\\\ResultInterface\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Results/ResultInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Match arm comparison between \'resource\' and \'resource\' is always true\\.$#',
	'identifier' => 'match.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/src/Schema/PromptMessage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$data of static method Swis\\\\McpClient\\\\Schema\\\\ToolAnnotation\\:\\:fromArray\\(\\) expects array\\{destructiveHint\\?\\: bool, idempotentHint\\?\\: bool, openWorldHint\\?\\: bool, readOnlyHint\\?\\: bool, title\\?\\: string\\}, array\\<string, mixed\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Schema/Tool.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\TransporterInterface\\:\\:initializeConnection\\(\\) has parameter \\$capabilities with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/TransporterInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\TransporterInterface\\:\\:initializeConnection\\(\\) has parameter \\$clientInfo with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/TransporterInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method promise\\(\\) on React\\\\Promise\\\\Deferred\\<mixed\\>\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Transporters/SseTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Transporters\\\\SseTransporter\\:\\:doSendRequest\\(\\) return type with generic interface React\\\\Promise\\\\PromiseInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Transporters/SseTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$response has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Transporters/SseTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$onRejected of method React\\\\Promise\\\\PromiseInterface\\<Psr\\\\Http\\\\Message\\\\ResponseInterface\\>\\:\\:then\\(\\) expects \\(callable\\(Throwable\\)\\: \\(React\\\\Promise\\\\PromiseInterface\\<void\\>\\|void\\)\\)\\|null, Closure\\(Exception\\)\\: void given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Transporters/SseTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$onRejected of method React\\\\Promise\\\\PromiseInterface\\<Psr\\\\Http\\\\Message\\\\ResponseInterface\\>\\:\\:then\\(\\) expects \\(callable\\(Throwable\\)\\: React\\\\Promise\\\\PromiseInterface\\<never\\>\\)\\|null, Closure\\(Exception\\)\\: never given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Transporters/SseTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$response has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Transporters/StdioTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$env of static method Swis\\\\McpClient\\\\Factories\\\\ProcessFactory\\:\\:createTransporterForProcess\\(\\) expects array\\<string, string\\>, array\\<string, bool\\|float\\|int\\|string\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Transporters/StdioTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Swis\\\\McpClient\\\\Transporters\\\\StdioTransporter\\:\\:\\$errorResource \\(resource\\|string\\|null\\) does not accept resource\\|false\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Transporters/StdioTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Swis\\\\McpClient\\\\Transporters\\\\StdioTransporter\\:\\:\\$inputResource \\(resource\\|string\\|null\\) does not accept resource\\|false\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Transporters/StdioTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Swis\\\\McpClient\\\\Transporters\\\\StdioTransporter\\:\\:\\$outputResource \\(resource\\|string\\|null\\) does not accept resource\\|false\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Transporters/StdioTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Transporters\\\\StreamableHttpTransporter\\:\\:initializeConnection\\(\\) has parameter \\$capabilities with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Transporters/StreamableHttpTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Transporters\\\\StreamableHttpTransporter\\:\\:initializeConnection\\(\\) has parameter \\$clientInfo with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Transporters/StreamableHttpTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Swis\\\\McpClient\\\\Transporters\\\\StreamableHttpTransporter\\:\\:initializeConnection\\(\\) should return array\\<string, bool\\|float\\|int\\|string\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Transporters/StreamableHttpTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$onFulfilled of method React\\\\Promise\\\\PromiseInterface\\<mixed\\>\\:\\:then\\(\\) expects \\(callable\\(mixed\\)\\: \\(Psr\\\\Http\\\\Message\\\\ResponseInterface\\|React\\\\Promise\\\\PromiseInterface\\<Psr\\\\Http\\\\Message\\\\ResponseInterface\\>\\)\\)\\|null, Closure\\(Psr\\\\Http\\\\Message\\\\ResponseInterface\\)\\: Psr\\\\Http\\\\Message\\\\ResponseInterface given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Transporters/StreamableHttpTransporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$onFulfilled of method React\\\\Promise\\\\PromiseInterface\\<mixed\\>\\:\\:then\\(\\) expects \\(callable\\(mixed\\)\\: \\(React\\\\Promise\\\\PromiseInterface\\<void\\>\\|void\\)\\)\\|null, Closure\\(Psr\\\\Http\\\\Message\\\\ResponseInterface\\)\\: void given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Transporters/StreamableHttpTransporter.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
