<?php

namespace Swis\McpClient\Tests;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class ClientIntegrationListToolsTest extends IntegrationTestCase
{
    /**
     * Test listing tools
     */
    #[RunInSeparateProcess]
    public function testListTools(): void
    {
        // Get available tools
        $tools = $this->client->listTools();

        // Verify we got the expected tools
        $this->assertCount(2, $tools->getTools(), 'Should have 2 tools available');

        // Check the first tool
        $echoTool = $tools->getTools()[0];
        $this->assertEquals('test.echo', $echoTool->getName());
        $this->assertEquals('Echo the input parameters back as the result', $echoTool->getDescription());
        $this->assertArrayHasKey('message', $echoTool->getSchema());

        // Check the second tool
        $addTool = $tools->getTools()[1];
        $this->assertEquals('test.add', $addTool->getName());
        $this->assertEquals('Add two numbers together', $addTool->getDescription());
        $this->assertArrayHasKey('a', $addTool->getSchema());
        $this->assertArrayHasKey('b', $addTool->getSchema());
    }
}
