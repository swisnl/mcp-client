<?php

namespace Swis\McpClient\Tests\Schema\Content;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Exceptions\UnknownResourceTypeException;
use Swis\McpClient\Schema\Annotation;
use Swis\McpClient\Schema\Content\EmbeddedResource;
use Swis\McpClient\Schema\Resource\BlobResourceContents;
use Swis\McpClient\Schema\Resource\TextResourceContents;
use Swis\McpClient\Schema\Role;

class EmbeddedResourceTest extends TestCase
{
    /**
     * Test creating an EmbeddedResource instance with text resource
     */
    public function testEmbeddedResourceWithTextResource(): void
    {
        $textResource = new TextResourceContents(
            text: 'This is text resource content',
            uri: 'file:///path/to/resource.txt',
            mimeType: 'text/plain'
        );

        $annotation = new Annotation(
            audience: [Role::ASSISTANT, Role::USER],
            priority: 0.8
        );

        $resource = new EmbeddedResource(
            resource: $textResource,
            annotations: $annotation
        );

        $this->assertSame($textResource, $resource->getResource());
        $this->assertSame($annotation, $resource->getAnnotations());
    }

    /**
     * Test creating an EmbeddedResource instance with blob resource
     */
    public function testEmbeddedResourceWithBlobResource(): void
    {
        $blobResource = new BlobResourceContents(
            blob: 'base64encodedblob==',
            uri: 'file:///path/to/resource.bin',
            mimeType: 'application/octet-stream'
        );

        $resource = new EmbeddedResource(
            resource: $blobResource
        );

        $this->assertSame($blobResource, $resource->getResource());
        $this->assertNull($resource->getAnnotations());
    }

    /**
     * Test converting EmbeddedResource with text resource to array
     */
    public function testEmbeddedResourceWithTextResourceToArray(): void
    {
        $textResource = new TextResourceContents(
            text: 'This is text resource content',
            uri: 'file:///path/to/resource.txt',
            mimeType: 'text/plain'
        );

        $annotation = new Annotation(
            audience: [Role::ASSISTANT, Role::USER],
            priority: 0.8
        );

        $resource = new EmbeddedResource(
            resource: $textResource,
            annotations: $annotation
        );

        $array = $resource->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('resource', $array);
        $this->assertArrayHasKey('annotations', $array);

        $this->assertEquals('resource', $array['type']);
        $this->assertEquals($textResource->toArray(), $array['resource']);
        $this->assertEquals($annotation->toArray(), $array['annotations']);
    }

    /**
     * Test converting EmbeddedResource with blob resource to array
     */
    public function testEmbeddedResourceWithBlobResourceToArray(): void
    {
        $blobResource = new BlobResourceContents(
            blob: 'base64encodedblob==',
            uri: 'file:///path/to/resource.bin',
            mimeType: 'application/octet-stream'
        );

        $resource = new EmbeddedResource(
            resource: $blobResource
        );

        $array = $resource->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('resource', $array);
        $this->assertArrayNotHasKey('annotations', $array);

        $this->assertEquals('resource', $array['type']);
        $this->assertEquals($blobResource->toArray(), $array['resource']);
    }

    /**
     * Test creating EmbeddedResource from array with text resource
     */
    public function testEmbeddedResourceFromArrayWithTextResource(): void
    {
        $annotationArray = [
            'audience' => ['assistant', 'user'],
            'priority' => 0.8,
        ];

        $resourceArray = [
            'text' => 'This is text resource content',
            'uri' => 'file:///path/to/resource.txt',
            'mimeType' => 'text/plain',
        ];

        $array = [
            'type' => 'resource',
            'resource' => $resourceArray,
            'annotations' => $annotationArray,
        ];

        $resource = EmbeddedResource::fromArray($array);

        $this->assertInstanceOf(TextResourceContents::class, $resource->getResource());

        $textResource = $resource->getResource();
        if ($textResource instanceof TextResourceContents) {
            $this->assertEquals('This is text resource content', $textResource->getText());
            $this->assertEquals('file:///path/to/resource.txt', $textResource->getUri());
            $this->assertEquals('text/plain', $textResource->getMimeType());
        }

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
     * Test creating EmbeddedResource from array with blob resource
     */
    public function testEmbeddedResourceFromArrayWithBlobResource(): void
    {
        $resourceArray = [
            'blob' => 'base64encodedblob==',
            'uri' => 'file:///path/to/resource.bin',
            'mimeType' => 'application/octet-stream',
        ];

        $array = [
            'type' => 'resource',
            'resource' => $resourceArray,
        ];

        $resource = EmbeddedResource::fromArray($array);

        $this->assertInstanceOf(BlobResourceContents::class, $resource->getResource());

        $blobResource = $resource->getResource();
        if ($blobResource instanceof BlobResourceContents) {
            $this->assertEquals('base64encodedblob==', $blobResource->getBlob());
            $this->assertEquals('file:///path/to/resource.bin', $blobResource->getUri());
            $this->assertEquals('application/octet-stream', $blobResource->getMimeType());
        }

        $this->assertNull($resource->getAnnotations());
    }

    /**
     * Test creating EmbeddedResource from array with unknown resource type
     */
    public function testEmbeddedResourceFromArrayWithUnknownResourceType(): void
    {
        $this->expectException(UnknownResourceTypeException::class);

        $resourceArray = [
            'unknown' => 'data',
            'uri' => 'file:///path/to/resource',
        ];

        $array = [
            'type' => 'resource',
            'resource' => $resourceArray,
        ];

        EmbeddedResource::fromArray($array);
    }

    /**
     * Test symmetry between toArray() and fromArray() with text resource
     */
    public function testSymmetryBetweenToArrayAndFromArrayWithTextResource(): void
    {
        $textResource = new TextResourceContents(
            text: 'This is text resource content',
            uri: 'file:///path/to/resource.txt',
            mimeType: 'text/plain'
        );

        $annotation = new Annotation(
            audience: [Role::ASSISTANT, Role::USER],
            priority: 0.8
        );

        $original = new EmbeddedResource(
            resource: $textResource,
            annotations: $annotation
        );

        $array = $original->toArray();
        $recreated = EmbeddedResource::fromArray($array);

        $this->assertInstanceOf(TextResourceContents::class, $recreated->getResource());

        $originalResource = $original->getResource();
        $recreatedResource = $recreated->getResource();

        if ($originalResource instanceof TextResourceContents && $recreatedResource instanceof TextResourceContents) {
            $this->assertEquals($originalResource->getText(), $recreatedResource->getText());
            $this->assertEquals($originalResource->getUri(), $recreatedResource->getUri());
            $this->assertEquals($originalResource->getMimeType(), $recreatedResource->getMimeType());
        }

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

    /**
     * Test symmetry between toArray() and fromArray() with blob resource
     */
    public function testSymmetryBetweenToArrayAndFromArrayWithBlobResource(): void
    {
        $blobResource = new BlobResourceContents(
            blob: 'base64encodedblob==',
            uri: 'file:///path/to/resource.bin',
            mimeType: 'application/octet-stream'
        );

        $original = new EmbeddedResource(
            resource: $blobResource
        );

        $array = $original->toArray();
        $recreated = EmbeddedResource::fromArray($array);

        $this->assertInstanceOf(BlobResourceContents::class, $recreated->getResource());

        $originalResource = $original->getResource();
        $recreatedResource = $recreated->getResource();

        if ($originalResource instanceof BlobResourceContents && $recreatedResource instanceof BlobResourceContents) {
            $this->assertEquals($originalResource->getBlob(), $recreatedResource->getBlob());
            $this->assertEquals($originalResource->getUri(), $recreatedResource->getUri());
            $this->assertEquals($originalResource->getMimeType(), $recreatedResource->getMimeType());
        }

        $this->assertNull($recreated->getAnnotations());
    }
}
