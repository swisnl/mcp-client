<?php

namespace Swis\McpClient\Tests\Schema;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Schema\Tool;
use Swis\McpClient\Schema\ToolAnnotation;

class ToolTest extends TestCase
{
    /**
     * Test creating a Tool instance with all parameters
     */
    public function testToolConstructionWithAllParameters(): void
    {
        $annotation = new ToolAnnotation(
            readOnlyHint: true,
            title: 'Echo Tool'
        );

        $schema = [
            'properties' => [
                'message' => [
                    'type' => 'string',
                    'description' => 'Message to echo',
                ],
            ],
            'required' => ['message'],
            'type' => 'object',
        ];

        $tool = new Tool(
            name: 'echo',
            schema: $schema,
            description: 'Echoes a message back',
            annotations: $annotation
        );

        $this->assertEquals('echo', $tool->getName());
        $this->assertEquals($schema, $tool->getSchema());
        $this->assertEquals('Echoes a message back', $tool->getDescription());
        $this->assertSame($annotation, $tool->getAnnotations());
    }

    /**
     * Test creating a Tool instance with minimal parameters
     */
    public function testToolConstructionWithMinimalParameters(): void
    {
        $tool = new Tool(
            name: 'minimal_tool',
            schema: []
        );

        $this->assertEquals('minimal_tool', $tool->getName());
        $this->assertEquals([], $tool->getSchema());
        $this->assertNull($tool->getDescription());
        $this->assertNull($tool->getAnnotations());
    }

    /**
     * Test converting Tool to array with all parameters
     */
    public function testToolToArrayWithAllParameters(): void
    {
        $annotation = new ToolAnnotation(
            readOnlyHint: true,
            title: 'Echo Tool'
        );

        $schema = [
            'properties' => [
                'message' => [
                    'type' => 'string',
                    'description' => 'Message to echo',
                ],
            ],
            'required' => ['message'],
            'type' => 'object',
        ];

        $tool = new Tool(
            name: 'echo',
            schema: $schema,
            description: 'Echoes a message back',
            annotations: $annotation
        );

        $array = $tool->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('inputSchema', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('annotations', $array);

        $this->assertEquals('echo', $array['name']);
        $this->assertEquals($schema, $array['inputSchema']);
        $this->assertEquals('Echoes a message back', $array['description']);
        $this->assertEquals($annotation->toArray(), $array['annotations']);
    }

    /**
     * Test converting Tool to array with minimal parameters
     */
    public function testToolToArrayWithMinimalParameters(): void
    {
        $tool = new Tool(
            name: 'minimal_tool',
            schema: []
        );

        $array = $tool->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('inputSchema', $array);
        $this->assertArrayNotHasKey('description', $array);
        $this->assertArrayNotHasKey('annotations', $array);

        $this->assertEquals('minimal_tool', $array['name']);
        $this->assertEquals([], $array['inputSchema']);
    }

    /**
     * Test creating Tool from array with all parameters
     */
    public function testToolFromArrayWithAllParameters(): void
    {
        $annotationArray = [
            'readOnlyHint' => true,
            'title' => 'Echo Tool',
        ];

        $schema = [
            'properties' => [
                'message' => [
                    'type' => 'string',
                    'description' => 'Message to echo',
                ],
            ],
            'required' => ['message'],
            'type' => 'object',
        ];

        $array = [
            'name' => 'echo',
            'inputSchema' => $schema,
            'description' => 'Echoes a message back',
            'annotations' => $annotationArray,
        ];

        $tool = Tool::fromArray($array);

        $this->assertEquals('echo', $tool->getName());
        $this->assertEquals($schema, $tool->getSchema());
        $this->assertEquals('Echoes a message back', $tool->getDescription());

        $annotations = $tool->getAnnotations();
        $this->assertInstanceOf(ToolAnnotation::class, $annotations);
        $this->assertTrue($annotations->getReadOnlyHint());
        $this->assertEquals('Echo Tool', $annotations->getTitle());
    }

    /**
     * Test creating Tool from array with minimal parameters
     */
    public function testToolFromArrayWithMinimalParameters(): void
    {
        $array = [
            'name' => 'minimal_tool',
        ];

        $tool = Tool::fromArray($array);

        $this->assertEquals('minimal_tool', $tool->getName());
        $this->assertEquals([], $tool->getSchema());
        $this->assertNull($tool->getDescription());
        $this->assertNull($tool->getAnnotations());
    }

    /**
     * Test symmetry between toArray() and fromArray()
     */
    public function testSymmetryBetweenToArrayAndFromArray(): void
    {
        $annotation = new ToolAnnotation(
            readOnlyHint: true,
            title: 'Echo Tool'
        );

        $schema = [
            'properties' => [
                'message' => [
                    'type' => 'string',
                    'description' => 'Message to echo',
                ],
            ],
            'required' => ['message'],
            'type' => 'object',
        ];

        $original = new Tool(
            name: 'echo',
            schema: $schema,
            description: 'Echoes a message back',
            annotations: $annotation
        );

        $array = $original->toArray();
        $recreated = Tool::fromArray($array);

        $this->assertEquals($original->getName(), $recreated->getName());
        $this->assertEquals($original->getSchema(), $recreated->getSchema());
        $this->assertEquals($original->getDescription(), $recreated->getDescription());

        $originalAnnotations = $original->getAnnotations();
        $recreatedAnnotations = $recreated->getAnnotations();

        $this->assertNotNull($recreatedAnnotations);
        if ($originalAnnotations && $recreatedAnnotations) {
            $this->assertEquals($originalAnnotations->getReadOnlyHint(), $recreatedAnnotations->getReadOnlyHint());
            $this->assertEquals($originalAnnotations->getTitle(), $recreatedAnnotations->getTitle());
        }
    }
}
