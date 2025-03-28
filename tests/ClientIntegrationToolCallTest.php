<?php

namespace Swis\McpClient\Tests;

use Swis\McpClient\Requests\CallToolRequest;

/**
 * Tests for tool call functionality with StdioTransporter
 */
class ClientIntegrationToolCallTest extends IntegrationTestCase
{
    /**
     * Test calling the echo tool
     */
    public function testEchoTool(): void
    {
        // Create a tool call request
        $request = new CallToolRequest(
            'test.echo',
            ['message' => 'Hello, world!']
        );

        // Call the tool
        $result = $this->client->callTool($request);

        // Verify the result
        $this->assertEquals('Hello, world!', $result->getContent()[0]->getText());
    }

    /**
     * Test calling the add tool - in a separate process to avoid loop issues
     *
     * @runInSeparateProcess
     */
    public function testAddTool(): void
    {
        // Create a tool call request
        $request = new CallToolRequest(
            'test.add',
            ['a' => 5, 'b' => 7]
        );

        // Call the tool
        $result = $this->client->callTool($request);

        // Verify the result
        $this->assertEquals(12, $result->getContent()[0]->getText());
    }

    /**
     * Test calling a non-existent tool - in a separate process to avoid loop issues
     *
     * @runInSeparateProcess
     */
    public function testNonExistentTool(): void
    {
        // Create a tool call request for a non-existent tool
        $request = new CallToolRequest(
            'test.nonexistent',
            ['foo' => 'bar']
        );

        // Call the tool and expect an error
        $result = $this->client->callTool($request);

        // Verify we got an error
        $this->assertInstanceOf(\Swis\McpClient\Results\JsonRpcError::class, $result);
        $this->assertEquals(-32601, $result->getCode());
        $this->assertStringContainsString('not found', $result->getMessage());
    }
}
