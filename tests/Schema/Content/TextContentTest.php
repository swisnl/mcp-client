<?php

namespace Swis\McpClient\Tests\Schema\Content;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Schema\Annotation;
use Swis\McpClient\Schema\Content\TextContent;
use Swis\McpClient\Schema\Role;

class TextContentTest extends TestCase
{
    /**
     * Test creating a TextContent instance with all parameters
     */
    public function testTextContentConstructionWithAllParameters(): void
    {
        $annotation = new Annotation(
            audience: [Role::ASSISTANT, Role::USER],
            priority: 0.8
        );

        $content = new TextContent(
            text: 'This is test text content',
            annotations: $annotation
        );

        $this->assertEquals('This is test text content', $content->getText());
        $this->assertSame($annotation, $content->getAnnotations());
    }

    /**
     * Test creating a TextContent instance with minimal parameters
     */
    public function testTextContentConstructionWithMinimalParameters(): void
    {
        $content = new TextContent(
            text: 'This is minimal text content'
        );

        $this->assertEquals('This is minimal text content', $content->getText());
        $this->assertNull($content->getAnnotations());
    }

    /**
     * Test converting TextContent to array with all parameters
     */
    public function testTextContentToArrayWithAllParameters(): void
    {
        $annotation = new Annotation(
            audience: [Role::ASSISTANT, Role::USER],
            priority: 0.8
        );

        $content = new TextContent(
            text: 'This is test text content',
            annotations: $annotation
        );

        $array = $content->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('text', $array);
        $this->assertArrayHasKey('annotations', $array);

        $this->assertEquals('text', $array['type']);
        $this->assertEquals('This is test text content', $array['text']);
        $this->assertEquals($annotation->toArray(), $array['annotations']);
    }

    /**
     * Test converting TextContent to array with minimal parameters
     */
    public function testTextContentToArrayWithMinimalParameters(): void
    {
        $content = new TextContent(
            text: 'This is minimal text content'
        );

        $array = $content->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('text', $array);
        $this->assertArrayNotHasKey('annotations', $array);

        $this->assertEquals('text', $array['type']);
        $this->assertEquals('This is minimal text content', $array['text']);
    }

    /**
     * Test creating TextContent from array with all parameters
     */
    public function testTextContentFromArrayWithAllParameters(): void
    {
        $annotationArray = [
            'audience' => ['assistant', 'user'],
            'priority' => 0.8,
        ];

        $array = [
            'type' => 'text',
            'text' => 'This is test text content',
            'annotations' => $annotationArray,
        ];

        $content = TextContent::fromArray($array);

        $this->assertEquals('This is test text content', $content->getText());

        $annotations = $content->getAnnotations();
        $this->assertInstanceOf(Annotation::class, $annotations);
        $audience = $annotations->getAudience();
        $this->assertNotNull($audience);
        $this->assertCount(2, $audience);
        $this->assertEquals(Role::ASSISTANT, $audience[0]);
        $this->assertEquals(Role::USER, $audience[1]);
        $this->assertEquals(0.8, $annotations->getPriority());
    }

    /**
     * Test creating TextContent from array with minimal parameters
     */
    public function testTextContentFromArrayWithMinimalParameters(): void
    {
        $array = [
            'type' => 'text',
            'text' => 'This is minimal text content',
        ];

        $content = TextContent::fromArray($array);

        $this->assertEquals('This is minimal text content', $content->getText());
        $this->assertNull($content->getAnnotations());
    }

    /**
     * Test symmetry between toArray() and fromArray()
     */
    public function testSymmetryBetweenToArrayAndFromArray(): void
    {
        $annotation = new Annotation(
            audience: [Role::ASSISTANT, Role::USER],
            priority: 0.8
        );

        $original = new TextContent(
            text: 'This is test text content',
            annotations: $annotation
        );

        $array = $original->toArray();
        $recreated = TextContent::fromArray($array);

        $this->assertEquals($original->getText(), $recreated->getText());

        $originalAnnotations = $original->getAnnotations();
        $recreatedAnnotations = $recreated->getAnnotations();

        $this->assertNotNull($recreatedAnnotations);
        if ($originalAnnotations && $recreatedAnnotations) {
            $this->assertEquals(
                array_map(fn ($role) => $role->value, $originalAnnotations->getAudience() ?? []),
                array_map(fn ($role) => $role->value, $recreatedAnnotations->getAudience() ?? [])
            );
            $this->assertEquals($originalAnnotations->getPriority(), $recreatedAnnotations->getPriority());
        }
    }
}
