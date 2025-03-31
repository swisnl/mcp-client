<?php

namespace Swis\McpClient\Tests;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Swis\McpClient\Results\ListResourcesResult;

class ClientIntegrationListResourcesTest extends IntegrationTestCase
{
    /**
     * Test listing resources
     */
    #[RunInSeparateProcess]
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
