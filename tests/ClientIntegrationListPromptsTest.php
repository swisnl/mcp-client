<?php

namespace Swis\McpClient\Tests;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Swis\McpClient\Results\ListPromptsResult;

class ClientIntegrationListPromptsTest extends IntegrationTestCase
{
    /**
     * Test listing prompts
     */
    #[RunInSeparateProcess]
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
