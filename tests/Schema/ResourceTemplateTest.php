<?php

namespace Swis\McpClient\Tests\Schema;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Schema\Annotation;
use Swis\McpClient\Schema\ResourceTemplate;
use Swis\McpClient\Schema\Role;

class ResourceTemplateTest extends TestCase
{
    /**
     * Test creating a ResourceTemplate instance with all parameters
     */
    public function testResourceTemplateConstructionWithAllParameters(): void
    {
        $annotation = new Annotation(
            audience: [Role::ASSISTANT, Role::USER],
            priority: 0.8
        );

        $template = new ResourceTemplate(
            name: 'Image Template',
            uri: 'file:///path/to/images/{id}.jpg',
            description: 'Template for image resources',
            mimeType: 'image/jpeg',
            annotations: $annotation
        );

        $this->assertEquals('Image Template', $template->getName());
        $this->assertEquals('file:///path/to/images/{id}.jpg', $template->getUri());
        $this->assertEquals('Template for image resources', $template->getDescription());
        $this->assertEquals('image/jpeg', $template->getMimeType());
        $this->assertSame($annotation, $template->getAnnotations());
    }

    /**
     * Test creating a ResourceTemplate instance with minimal parameters
     */
    public function testResourceTemplateConstructionWithMinimalParameters(): void
    {
        $template = new ResourceTemplate(
            name: 'Minimal Template',
            uri: 'file:///path/to/resources/{id}'
        );

        $this->assertEquals('Minimal Template', $template->getName());
        $this->assertEquals('file:///path/to/resources/{id}', $template->getUri());
        $this->assertNull($template->getDescription());
        $this->assertNull($template->getMimeType());
        $this->assertNull($template->getAnnotations());
    }

    /**
     * Test converting ResourceTemplate to array with all parameters
     */
    public function testResourceTemplateToArrayWithAllParameters(): void
    {
        $annotation = new Annotation(
            audience: [Role::ASSISTANT, Role::USER],
            priority: 0.8
        );

        $template = new ResourceTemplate(
            name: 'Image Template',
            uri: 'file:///path/to/images/{id}.jpg',
            description: 'Template for image resources',
            mimeType: 'image/jpeg',
            annotations: $annotation
        );

        $array = $template->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('uri', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('mimeType', $array);
        $this->assertArrayHasKey('annotations', $array);

        $this->assertEquals('Image Template', $array['name']);
        $this->assertEquals('file:///path/to/images/{id}.jpg', $array['uri']);
        $this->assertEquals('Template for image resources', $array['description']);
        $this->assertEquals('image/jpeg', $array['mimeType']);
        $this->assertEquals($annotation->toArray(), $array['annotations']);
    }

    /**
     * Test converting ResourceTemplate to array with minimal parameters
     */
    public function testResourceTemplateToArrayWithMinimalParameters(): void
    {
        $template = new ResourceTemplate(
            name: 'Minimal Template',
            uri: 'file:///path/to/resources/{id}'
        );

        $array = $template->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('uri', $array);
        $this->assertArrayNotHasKey('description', $array);
        $this->assertArrayNotHasKey('mimeType', $array);
        $this->assertArrayNotHasKey('annotations', $array);

        $this->assertEquals('Minimal Template', $array['name']);
        $this->assertEquals('file:///path/to/resources/{id}', $array['uri']);
    }

    /**
     * Test creating ResourceTemplate from array with all parameters
     */
    public function testResourceTemplateFromArrayWithAllParameters(): void
    {
        $annotationArray = [
            'audience' => ['assistant', 'user'],
            'priority' => 0.8,
        ];

        $array = [
            'name' => 'Image Template',
            'uri' => 'file:///path/to/images/{id}.jpg',
            'description' => 'Template for image resources',
            'mimeType' => 'image/jpeg',
            'annotations' => $annotationArray,
        ];

        $template = ResourceTemplate::fromArray($array);

        $this->assertEquals('Image Template', $template->getName());
        $this->assertEquals('file:///path/to/images/{id}.jpg', $template->getUri());
        $this->assertEquals('Template for image resources', $template->getDescription());
        $this->assertEquals('image/jpeg', $template->getMimeType());

        $annotations = $template->getAnnotations();
        $this->assertInstanceOf(Annotation::class, $annotations);
        $audience = $annotations->getAudience();
        $this->assertNotNull($audience);
        $this->assertCount(2, $audience);
        $this->assertEquals(Role::ASSISTANT, $audience[0]);
        $this->assertEquals(Role::USER, $audience[1]);
        $this->assertEquals(0.8, $annotations->getPriority());
    }

    /**
     * Test creating ResourceTemplate from array with minimal parameters
     */
    public function testResourceTemplateFromArrayWithMinimalParameters(): void
    {
        $array = [
            'name' => 'Minimal Template',
            'uri' => 'file:///path/to/resources/{id}',
        ];

        $template = ResourceTemplate::fromArray($array);

        $this->assertEquals('Minimal Template', $template->getName());
        $this->assertEquals('file:///path/to/resources/{id}', $template->getUri());
        $this->assertNull($template->getDescription());
        $this->assertNull($template->getMimeType());
        $this->assertNull($template->getAnnotations());
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

        $original = new ResourceTemplate(
            name: 'Image Template',
            uri: 'file:///path/to/images/{id}.jpg',
            description: 'Template for image resources',
            mimeType: 'image/jpeg',
            annotations: $annotation
        );

        $array = $original->toArray();
        $recreated = ResourceTemplate::fromArray($array);

        $this->assertEquals($original->getName(), $recreated->getName());
        $this->assertEquals($original->getUri(), $recreated->getUri());
        $this->assertEquals($original->getDescription(), $recreated->getDescription());
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
