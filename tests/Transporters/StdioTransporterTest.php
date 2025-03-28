<?php

namespace Swis\McpClient\Tests\Transporters;

use Swis\McpClient\Tests\IntegrationTestCase;

/**
 * Integration tests for the StdioTransporter
 */
class StdioTransporterTest extends IntegrationTestCase
{
    /**
     * Test the full client flow with a process-based StdioTransporter
     *
     * @runInSeparateProcess
     */
    public function testClientFlowWithProcess(): void
    {
        // Test ping request
        $pingResult = $this->client->ping();
        $this->assertTrue($pingResult);

        // Test listing tools
        $toolsResult = $this->client->listTools();
        $this->assertCount(2, $toolsResult->getTools());

        // Verify tool details
        $tools = $toolsResult->getTools();
        $this->assertEquals('test.echo', $tools[0]->getName());
        $this->assertEquals('test.add', $tools[1]->getName());
    }
}
