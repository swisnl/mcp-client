<?php

namespace Swis\McpClient\Tests;

use React\EventLoop\Loop;
use Swis\McpClient\Requests\ListPromptsRequest;
use Swis\McpClient\Requests\ListResourcesRequest;
use Swis\McpClient\Requests\ListToolsRequest;

class ClientIntegrationAsyncTest extends IntegrationTestCase
{
    /**
     * Test asynchronous requests
     *
     * @runInSeparateProcess
     */
    public function testAsyncRequests(): void
    {
        // Create a deferred that will be resolved when all callbacks have been invoked
        $promises = [];
        $results = [
            'ping' => null,
            'tools' => null,
            'resources' => null,
            'prompts' => null,
        ];

        // Send multiple requests asynchronously
        $this->client->sendRequest(new ListToolsRequest(), function ($result) use (&$results) {
            $results['tools'] = $result;
        });

        $this->client->sendRequest(new ListResourcesRequest(), function ($result) use (&$results) {
            $results['resources'] = $result;
        });

        $this->client->sendRequest(new ListPromptsRequest(), function ($result) use (&$results) {
            $results['prompts'] = $result;
        });

        // Run the event loop briefly to process the requests
        Loop::get()->addTimer(0.5, function () use (&$results) {
            Loop::get()->stop();

            // Verify all callbacks were invoked
            $this->assertNotNull($results['tools'], 'Tools result should be populated');
            $this->assertNotNull($results['resources'], 'Resources result should be populated');
            $this->assertNotNull($results['prompts'], 'Prompts result should be populated');

            // Verify result types
            $this->assertCount(2, $results['tools']->getTools());
        });

        // Start the event loop
        Loop::get()->run();
    }
}
