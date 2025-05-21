<?php

namespace Swis\McpClient\Tests\Results;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Results\ListPromptsResult;

class ListPromptsResultTest extends TestCase
{
    /**
     * Test creating ListPromptsResult from array
     */
    public function testFromArray(): void
    {
        $requestId = 'test-request-123';
        $promptData = [
            'name' => 'testPrompt',
            'description' => 'A test prompt',
            'arguments' => [
                'name' => 'testArgument',
                'required' => false,
            ],
        ];

        $data = [
            'prompts' => [$promptData],
            '_meta' => ['test' => true],
        ];

        $result = ListPromptsResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertEquals(['test' => true], $result->getMeta());
        $this->assertNull($result->getNextCursor());

        // Test prompts
        $prompts = $result->getPrompts();
        $this->assertCount(1, $prompts);
        $this->assertEquals('testPrompt', $prompts[0]['name']);
        $this->assertEquals('A test prompt', $prompts[0]['description']);
    }

    /**
     * Test creating ListPromptsResult from array with cursor
     */
    public function testFromArrayWithCursor(): void
    {
        $requestId = 'test-request-456';
        $promptData1 = [
            'name' => 'prompt1',
            'description' => 'First prompt',
        ];

        $promptData2 = [
            'name' => 'prompt2',
            'description' => 'Second prompt',
        ];

        $data = [
            'prompts' => [$promptData1, $promptData2],
            'nextCursor' => 'next-page-token',
        ];

        $result = ListPromptsResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertNull($result->getMeta());
        $this->assertEquals('next-page-token', $result->getNextCursor());

        // Test prompts
        $prompts = $result->getPrompts();
        $this->assertCount(2, $prompts);
        $this->assertEquals('prompt1', $prompts[0]['name']);
        $this->assertEquals('prompt2', $prompts[1]['name']);
        $this->assertEquals('First prompt', $prompts[0]['description']);
        $this->assertEquals('Second prompt', $prompts[1]['description']);
    }

    /**
     * Test toArray method
     */
    public function testToArray(): void
    {
        $requestId = 'test-request-789';
        $prompts = [
            [
                'name' => 'testPrompt',
                'description' => 'A test prompt',
            ],
        ];

        $result = new ListPromptsResult($requestId, $prompts, 'next-token', ['test' => true]);

        $array = $result->toArray();

        $this->assertArrayHasKey('prompts', $array);
        $this->assertCount(1, $array['prompts']);
        $this->assertEquals('testPrompt', $array['prompts'][0]['name']);
        $this->assertEquals('A test prompt', $array['prompts'][0]['description']);
        $this->assertEquals('next-token', $array['nextCursor']);

        // Test jsonSerialize includes meta
        $json = $result->jsonSerialize();
        $this->assertArrayHasKey('_meta', $json);
        $this->assertEquals(['test' => true], $json['_meta']);
    }
}
