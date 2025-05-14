<?php

namespace Swis\McpClient\Tests\Schema;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Schema\Annotation;
use Swis\McpClient\Schema\Resource;
use Swis\McpClient\Schema\Role;

class ResourceTest extends TestCase
{
    /**
     * Test creating a Resource instance with all parameters
     */
    public function testResourceConstructionWithAllParameters(): void
    {
        $annotation = new Annotation(
            audience: [Role::ASSISTANT, Role::USER],
            priority: 0.8
        );

        $resource = new Resource(
            name: 'test-image.jpg',
            uri: 'file:///path/to/image.jpg',
            description: 'A test image',
            mimeType: 'image/jpeg',
            size: 1024,
            annotations: $annotation
        );

        $this->assertEquals('test-image.jpg', $resource->getName());
        $this->assertEquals('file:///path/to/image.jpg', $resource->getUri());
        $this->assertEquals('A test image', $resource->getDescription());
        $this->assertEquals('image/jpeg', $resource->getMimeType());
        $this->assertEquals(1024, $resource->getSize());
        $this->assertSame($annotation, $resource->getAnnotations());
    }

    /**
     * Test creating a Resource instance with minimal parameters
     */
    public function testResourceConstructionWithMinimalParameters(): void
    {
        $resource = new Resource(
            name: 'minimal-resource',
            uri: 'file:///path/to/resource'
        );

        $this->assertEquals('minimal-resource', $resource->getName());
        $this->assertEquals('file:///path/to/resource', $resource->getUri());
        $this->assertNull($resource->getDescription());
        $this->assertNull($resource->getMimeType());
        $this->assertNull($resource->getSize());
        $this->assertNull($resource->getAnnotations());
    }

    /**
     * Test converting Resource to array with all parameters
     */
    public function testResourceToArrayWithAllParameters(): void
    {
        $annotation = new Annotation(
            audience: [Role::ASSISTANT, Role::USER],
            priority: 0.8
        );

        $resource = new Resource(
            name: 'test-image.jpg',
            uri: 'file:///path/to/image.jpg',
            description: 'A test image',
            mimeType: 'image/jpeg',
            size: 1024,
            annotations: $annotation
        );

        $array = $resource->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('uri', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('mimeType', $array);
        $this->assertArrayHasKey('size', $array);
        $this->assertArrayHasKey('annotations', $array);

        $this->assertEquals('test-image.jpg', $array['name']);
        $this->assertEquals('file:///path/to/image.jpg', $array['uri']);
        $this->assertEquals('A test image', $array['description']);
        $this->assertEquals('image/jpeg', $array['mimeType']);
        $this->assertEquals(1024, $array['size']);
        $this->assertEquals($annotation->toArray(), $array['annotations']);
    }

    /**
     * Test converting Resource to array with minimal parameters
     */
    public function testResourceToArrayWithMinimalParameters(): void
    {
        $resource = new Resource(
            name: 'minimal-resource',
            uri: 'file:///path/to/resource'
        );

        $array = $resource->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('uri', $array);
        $this->assertArrayNotHasKey('description', $array);
        $this->assertArrayNotHasKey('mimeType', $array);
        $this->assertArrayNotHasKey('size', $array);
        $this->assertArrayNotHasKey('annotations', $array);

        $this->assertEquals('minimal-resource', $array['name']);
        $this->assertEquals('file:///path/to/resource', $array['uri']);
    }

    /**
     * Test creating Resource from array with all parameters
     */
    public function testResourceFromArrayWithAllParameters(): void
    {
        $annotationArray = [
            'audience' => ['assistant', 'user'],
            'priority' => 0.8,
        ];

        $array = [
            'name' => 'test-image.jpg',
            'uri' => 'file:///path/to/image.jpg',
            'description' => 'A test image',
            'mimeType' => 'image/jpeg',
            'size' => 1024,
            'annotations' => $annotationArray,
        ];

        $resource = Resource::fromArray($array);

        $this->assertEquals('test-image.jpg', $resource->getName());
        $this->assertEquals('file:///path/to/image.jpg', $resource->getUri());
        $this->assertEquals('A test image', $resource->getDescription());
        $this->assertEquals('image/jpeg', $resource->getMimeType());
        $this->assertEquals(1024, $resource->getSize());

        $annotations = $resource->getAnnotations();
        $this->assertInstanceOf(Annotation::class, $annotations);
        $audience = $annotations->getAudience();
        $this->assertNotNull($audience);
        $this->assertCount(2, $audience);
        $this->assertEquals(Role::ASSISTANT, $audience[0]);
        $this->assertEquals(Role::USER, $audience[1]);
        $this->assertEquals(0.8, $annotations->getPriority());
    }

    /**
     * Test creating Resource from array with minimal parameters
     */
    public function testResourceFromArrayWithMinimalParameters(): void
    {
        $array = [
            'name' => 'minimal-resource',
            'uri' => 'file:///path/to/resource',
        ];

        $resource = Resource::fromArray($array);

        $this->assertEquals('minimal-resource', $resource->getName());
        $this->assertEquals('file:///path/to/resource', $resource->getUri());
        $this->assertNull($resource->getDescription());
        $this->assertNull($resource->getMimeType());
        $this->assertNull($resource->getSize());
        $this->assertNull($resource->getAnnotations());
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

        $original = new Resource(
            name: 'test-image.jpg',
            uri: 'file:///path/to/image.jpg',
            description: 'A test image',
            mimeType: 'image/jpeg',
            size: 1024,
            annotations: $annotation
        );

        $array = $original->toArray();
        $recreated = Resource::fromArray($array);

        $this->assertEquals($original->getName(), $recreated->getName());
        $this->assertEquals($original->getUri(), $recreated->getUri());
        $this->assertEquals($original->getDescription(), $recreated->getDescription());
        $this->assertEquals($original->getMimeType(), $recreated->getMimeType());
        $this->assertEquals($original->getSize(), $recreated->getSize());

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
