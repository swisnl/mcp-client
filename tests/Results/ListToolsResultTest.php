<?php

namespace Swis\McpClient\Tests\Results;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Results\ListToolsResult;
use Swis\McpClient\Schema\Tool;
use Swis\McpClient\Schema\ToolAnnotation;

class ListToolsResultTest extends TestCase
{
    /**
     * Test creating ListToolsResult from array
     */
    public function testFromArray(): void
    {
        $requestId = 'test-request-123';
        $toolData = [
            'name' => 'testTool',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'param1' => ['type' => 'string'],
                    'param2' => ['type' => 'number'],
                ],
                'required' => ['param1'],
            ],
            'description' => 'A test tool',
        ];

        $data = [
            'tools' => [$toolData],
            '_meta' => ['test' => true],
        ];

        $result = ListToolsResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertEquals(['test' => true], $result->getMeta());
        $this->assertNull($result->getNextCursor());

        // Test tools
        $tools = $result->getTools();
        $this->assertCount(1, $tools);
        $this->assertInstanceOf(Tool::class, $tools[0]);
        $this->assertEquals('testTool', $tools[0]->getName());
        $this->assertEquals('A test tool', $tools[0]->getDescription());

        // Test schema
        $schema = $tools[0]->getSchema();
        $this->assertEquals('object', $schema['type']);
        $this->assertArrayHasKey('properties', $schema);
        $this->assertArrayHasKey('required', $schema);
    }

    /**
     * Test creating ListToolsResult from array with annotations and cursor
     */
    public function testFromArrayWithAnnotationsAndCursor(): void
    {
        $requestId = 'test-request-456';
        $toolData = [
            'name' => 'advancedTool',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'param1' => ['type' => 'string'],
                ],
                'required' => ['param1'],
            ],
            'description' => 'An advanced tool',
            'annotations' => [
                'readOnlyHint' => true,
                'title' => 'Advanced Test Tool',
            ],
        ];

        $data = [
            'tools' => [$toolData],
            'nextCursor' => 'next-page-token',
        ];

        $result = ListToolsResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertNull($result->getMeta());
        $this->assertEquals('next-page-token', $result->getNextCursor());

        // Test tools
        $tools = $result->getTools();
        $this->assertCount(1, $tools);
        $this->assertInstanceOf(Tool::class, $tools[0]);
        $this->assertEquals('advancedTool', $tools[0]->getName());

        // Test annotations
        $annotations = $tools[0]->getAnnotations();
        $this->assertInstanceOf(ToolAnnotation::class, $annotations);
        $this->assertTrue($annotations->getReadOnlyHint());
        $this->assertEquals('Advanced Test Tool', $annotations->getTitle());
    }

    /**
     * Test creating ListToolsResult from array with multiple tools
     */
    public function testFromArrayWithMultipleTools(): void
    {
        $requestId = 'test-request-789';
        $tool1Data = [
            'name' => 'tool1',
            'description' => 'First tool',
        ];

        $tool2Data = [
            'name' => 'tool2',
            'description' => 'Second tool',
        ];

        $data = [
            'tools' => [$tool1Data, $tool2Data],
        ];

        $result = ListToolsResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertNull($result->getMeta());

        // Test tools
        $tools = $result->getTools();
        $this->assertCount(2, $tools);
        $this->assertEquals('tool1', $tools[0]->getName());
        $this->assertEquals('tool2', $tools[1]->getName());
        $this->assertEquals('First tool', $tools[0]->getDescription());
        $this->assertEquals('Second tool', $tools[1]->getDescription());
    }

    /**
     * Test toArray method
     */
    public function testToArray(): void
    {
        $requestId = 'test-request-123';
        $tool = new Tool(
            'testTool',
            [
                'type' => 'object',
                'properties' => [
                    'param1' => ['type' => 'string'],
                ],
                'required' => ['param1'],
            ],
            'Test tool'
        );

        $result = new ListToolsResult($requestId, [$tool], 'next-token', ['test' => true]);

        $array = $result->toArray();

        $this->assertArrayHasKey('tools', $array);
        $this->assertCount(1, $array['tools']);
        $this->assertEquals('testTool', $array['tools'][0]['name']);
        $this->assertEquals('Test tool', $array['tools'][0]['description']);
        $this->assertEquals('next-token', $array['nextCursor']);

        // Test jsonSerialize includes meta
        $json = $result->jsonSerialize();
        $this->assertArrayHasKey('_meta', $json);
        $this->assertEquals(['test' => true], $json['_meta']);
    }
}
