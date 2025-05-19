<?php

namespace Swis\McpClient\Tests\Results;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Results\GetPromptResult;
use Swis\McpClient\Schema\Content\TextContent;
use Swis\McpClient\Schema\PromptMessage;
use Swis\McpClient\Schema\Role;

class GetPromptResultTest extends TestCase
{
    /**
     * Test creating GetPromptResult from array
     */
    public function testFromArray(): void
    {
        $requestId = 'test-request-123';
        $description = 'Test prompt description';
        $messageData = [
            'role' => 'user',
            'content' => [
                'type' => 'text',
                'text' => 'This is a test message',
            ],
        ];

        $data = [
            'description' => $description,
            'messages' => [$messageData],
            '_meta' => ['test' => true],
        ];

        $result = GetPromptResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertEquals($description, $result->getDescription());
        $this->assertEquals(['test' => true], $result->getMeta());

        // Test messages
        $messages = $result->getMessages();
        $this->assertCount(1, $messages);
        $this->assertInstanceOf(PromptMessage::class, $messages[0]);
        $this->assertEquals(Role::USER, $messages[0]->getRole());

        $content = $messages[0]->getUri();
        $this->assertInstanceOf(TextContent::class, $content);
        $this->assertEquals('This is a test message', $content->getText());
    }

    /**
     * Test creating GetPromptResult from array with multiple messages
     */
    public function testFromArrayWithMultipleMessages(): void
    {
        $requestId = 'test-request-456';
        $description = 'Test with multiple messages';

        $userMessageData = [
            'role' => 'user',
            'content' => [
                'type' => 'text',
                'text' => 'User question',
            ],
        ];

        $assistantMessageData = [
            'role' => 'assistant',
            'content' => [
                'type' => 'text',
                'text' => 'Assistant response',
            ],
        ];

        $data = [
            'description' => $description,
            'messages' => [$userMessageData, $assistantMessageData],
        ];

        $result = GetPromptResult::fromArray($data, $requestId);

        // Verify basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertEquals($description, $result->getDescription());
        $this->assertNull($result->getMeta());

        // Verify messages
        $messages = $result->getMessages();
        $this->assertCount(2, $messages);

        // First message (user)
        $this->assertEquals(Role::USER, $messages[0]->getRole());
        $content1 = $messages[0]->getUri();
        $this->assertInstanceOf(TextContent::class, $content1);
        $this->assertEquals('User question', $content1->getText());

        // Second message (assistant)
        $this->assertEquals(Role::ASSISTANT, $messages[1]->getRole());
        $content2 = $messages[1]->getUri();
        $this->assertInstanceOf(TextContent::class, $content2);
        $this->assertEquals('Assistant response', $content2->getText());
    }
}
