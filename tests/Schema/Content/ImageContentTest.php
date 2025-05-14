<?php

namespace Swis\McpClient\Tests\Schema\Content;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Schema\Annotation;
use Swis\McpClient\Schema\Content\ImageContent;
use Swis\McpClient\Schema\Role;

class ImageContentTest extends TestCase
{
    /**
     * Test creating an ImageContent instance with all parameters
     */
    public function testImageContentConstructionWithAllParameters(): void
    {
        $annotation = new Annotation(
            audience: [Role::ASSISTANT, Role::USER],
            priority: 0.8
        );

        $content = new ImageContent(
            data: 'base64encodeddata==',
            mimeType: 'image/png',
            annotations: $annotation
        );

        $this->assertEquals('base64encodeddata==', $content->getData());
        $this->assertEquals('image/png', $content->getMimeType());
        $this->assertSame($annotation, $content->getAnnotations());
    }

    /**
     * Test creating an ImageContent instance with minimal parameters
     */
    public function testImageContentConstructionWithMinimalParameters(): void
    {
        $content = new ImageContent(
            data: 'base64encodeddata==',
            mimeType: 'image/jpeg'
        );

        $this->assertEquals('base64encodeddata==', $content->getData());
        $this->assertEquals('image/jpeg', $content->getMimeType());
        $this->assertNull($content->getAnnotations());
    }

    /**
     * Test converting ImageContent to array with all parameters
     */
    public function testImageContentToArrayWithAllParameters(): void
    {
        $annotation = new Annotation(
            audience: [Role::ASSISTANT, Role::USER],
            priority: 0.8
        );

        $content = new ImageContent(
            data: 'base64encodeddata==',
            mimeType: 'image/png',
            annotations: $annotation
        );

        $array = $content->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('data', $array);
        $this->assertArrayHasKey('mimeType', $array);
        $this->assertArrayHasKey('annotations', $array);

        $this->assertEquals('image', $array['type']);
        $this->assertEquals('base64encodeddata==', $array['data']);
        $this->assertEquals('image/png', $array['mimeType']);
        $this->assertEquals($annotation->toArray(), $array['annotations']);
    }

    /**
     * Test converting ImageContent to array with minimal parameters
     */
    public function testImageContentToArrayWithMinimalParameters(): void
    {
        $content = new ImageContent(
            data: 'base64encodeddata==',
            mimeType: 'image/jpeg'
        );

        $array = $content->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('data', $array);
        $this->assertArrayHasKey('mimeType', $array);
        $this->assertArrayNotHasKey('annotations', $array);

        $this->assertEquals('image', $array['type']);
        $this->assertEquals('base64encodeddata==', $array['data']);
        $this->assertEquals('image/jpeg', $array['mimeType']);
    }

    /**
     * Test creating ImageContent from array with all parameters
     */
    public function testImageContentFromArrayWithAllParameters(): void
    {
        $annotationArray = [
            'audience' => ['assistant', 'user'],
            'priority' => 0.8,
        ];

        $array = [
            'type' => 'image',
            'data' => 'base64encodeddata==',
            'mimeType' => 'image/png',
            'annotations' => $annotationArray,
        ];

        $content = ImageContent::fromArray($array);

        $this->assertEquals('base64encodeddata==', $content->getData());
        $this->assertEquals('image/png', $content->getMimeType());

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
     * Test creating ImageContent from array with minimal parameters
     */
    public function testImageContentFromArrayWithMinimalParameters(): void
    {
        $array = [
            'type' => 'image',
            'data' => 'base64encodeddata==',
            'mimeType' => 'image/jpeg',
        ];

        $content = ImageContent::fromArray($array);

        $this->assertEquals('base64encodeddata==', $content->getData());
        $this->assertEquals('image/jpeg', $content->getMimeType());
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

        $original = new ImageContent(
            data: 'base64encodeddata==',
            mimeType: 'image/png',
            annotations: $annotation
        );

        $array = $original->toArray();
        $recreated = ImageContent::fromArray($array);

        $this->assertEquals($original->getData(), $recreated->getData());
        $this->assertEquals($original->getMimeType(), $recreated->getMimeType());

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
