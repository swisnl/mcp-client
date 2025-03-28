<?php

namespace Swis\McpClient\Tests;

use Swis\McpClient\Results\ListPromptsResult;

class ClientIntegrationListPromptsTest extends IntegrationTestCase
{
    /**
     * Test listing prompts
     *
     * @runInSeparateProcess
     */
    public function testListPrompts(): void
    {
        // Get available prompts
        $promptsResult = $this->client->listPrompts();

        // Verify the result
        $this->assertInstanceOf(ListPromptsResult::class, $promptsResult);
        $this->assertIsArray($promptsResult->getPrompts());
        $this->assertEmpty($promptsResult->getPrompts(), 'Mock server has no prompts');
    }
}
