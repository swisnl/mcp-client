<?php

namespace Swis\McpClient\Tests\Schema;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Schema\ToolAnnotation;

class ToolAnnotationTest extends TestCase
{
    /**
     * Test creating a ToolAnnotation instance with constructor
     */
    public function testToolAnnotationConstruction(): void
    {
        $annotation = new ToolAnnotation(
            destructiveHint: true,
            idempotentHint: false,
            openWorldHint: true,
            readOnlyHint: false,
            title: 'Test Tool'
        );

        $this->assertTrue($annotation->getDestructiveHint());
        $this->assertFalse($annotation->getIdempotentHint());
        $this->assertTrue($annotation->getOpenWorldHint());
        $this->assertFalse($annotation->getReadOnlyHint());
        $this->assertEquals('Test Tool', $annotation->getTitle());
    }

    /**
     * Test creating a ToolAnnotation instance with partial parameters
     */
    public function testToolAnnotationPartialConstruction(): void
    {
        $annotation = new ToolAnnotation(
            readOnlyHint: true,
            title: 'Partial Tool'
        );

        $this->assertNull($annotation->getDestructiveHint());
        $this->assertNull($annotation->getIdempotentHint());
        $this->assertNull($annotation->getOpenWorldHint());
        $this->assertTrue($annotation->getReadOnlyHint());
        $this->assertEquals('Partial Tool', $annotation->getTitle());
    }

    /**
     * Test converting ToolAnnotation to array
     */
    public function testToolAnnotationToArray(): void
    {
        $annotation = new ToolAnnotation(
            destructiveHint: true,
            idempotentHint: false,
            openWorldHint: true,
            readOnlyHint: false,
            title: 'Test Tool'
        );

        $array = $annotation->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('destructiveHint', $array);
        $this->assertArrayHasKey('idempotentHint', $array);
        $this->assertArrayHasKey('openWorldHint', $array);
        $this->assertArrayHasKey('readOnlyHint', $array);
        $this->assertArrayHasKey('title', $array);

        $this->assertTrue($array['destructiveHint']);
        $this->assertFalse($array['idempotentHint']);
        $this->assertTrue($array['openWorldHint']);
        $this->assertFalse($array['readOnlyHint']);
        $this->assertEquals('Test Tool', $array['title']);
    }

    /**
     * Test converting partial ToolAnnotation to array - only non-null values should be included
     */
    public function testPartialToolAnnotationToArray(): void
    {
        $annotation = new ToolAnnotation(
            readOnlyHint: true,
            title: 'Partial Tool'
        );

        $array = $annotation->toArray();

        $this->assertIsArray($array);
        $this->assertArrayNotHasKey('destructiveHint', $array);
        $this->assertArrayNotHasKey('idempotentHint', $array);
        $this->assertArrayNotHasKey('openWorldHint', $array);
        $this->assertArrayHasKey('readOnlyHint', $array);
        $this->assertArrayHasKey('title', $array);

        $this->assertTrue($array['readOnlyHint']);
        $this->assertEquals('Partial Tool', $array['title']);
    }

    /**
     * Test creating ToolAnnotation from array
     */
    public function testToolAnnotationFromArray(): void
    {
        $array = [
            'destructiveHint' => true,
            'idempotentHint' => false,
            'openWorldHint' => true,
            'readOnlyHint' => false,
            'title' => 'Test Tool',
        ];

        $annotation = ToolAnnotation::fromArray($array);

        $this->assertTrue($annotation->getDestructiveHint());
        $this->assertFalse($annotation->getIdempotentHint());
        $this->assertTrue($annotation->getOpenWorldHint());
        $this->assertFalse($annotation->getReadOnlyHint());
        $this->assertEquals('Test Tool', $annotation->getTitle());
    }

    /**
     * Test creating ToolAnnotation from partial array
     */
    public function testToolAnnotationFromPartialArray(): void
    {
        $array = [
            'readOnlyHint' => true,
            'title' => 'Partial Tool',
        ];

        $annotation = ToolAnnotation::fromArray($array);

        $this->assertNull($annotation->getDestructiveHint());
        $this->assertNull($annotation->getIdempotentHint());
        $this->assertNull($annotation->getOpenWorldHint());
        $this->assertTrue($annotation->getReadOnlyHint());
        $this->assertEquals('Partial Tool', $annotation->getTitle());
    }

    /**
     * Test symmetry between toArray() and fromArray()
     */
    public function testSymmetryBetweenToArrayAndFromArray(): void
    {
        $original = new ToolAnnotation(
            destructiveHint: true,
            idempotentHint: false,
            openWorldHint: true,
            readOnlyHint: false,
            title: 'Test Tool'
        );

        $array = $original->toArray();
        $recreated = ToolAnnotation::fromArray($array);

        $this->assertEquals($original->getDestructiveHint(), $recreated->getDestructiveHint());
        $this->assertEquals($original->getIdempotentHint(), $recreated->getIdempotentHint());
        $this->assertEquals($original->getOpenWorldHint(), $recreated->getOpenWorldHint());
        $this->assertEquals($original->getReadOnlyHint(), $recreated->getReadOnlyHint());
        $this->assertEquals($original->getTitle(), $recreated->getTitle());
    }
}
