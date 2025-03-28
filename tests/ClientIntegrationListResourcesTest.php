<?php

namespace Swis\McpClient\Tests;

use Swis\McpClient\Results\ListResourcesResult;

class ClientIntegrationListResourcesTest extends IntegrationTestCase
{
    /**
     * Test listing resources
     *
     * @runInSeparateProcess
     */
    public function testListResources(): void
    {
        // Get available resources
        $resourcesResult = $this->client->listResources();

        // Verify the result
        $this->assertInstanceOf(ListResourcesResult::class, $resourcesResult);
        $this->assertIsArray($resourcesResult->getResources());
        $this->assertEmpty($resourcesResult->getResources(), 'Mock server has no resources');
    }
}
