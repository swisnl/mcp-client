<?php

namespace Swis\McpClient\Tests\Results;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Results\CallToolResult;
use Swis\McpClient\Schema\Annotation;
use Swis\McpClient\Schema\Content\EmbeddedResource;
use Swis\McpClient\Schema\Content\ImageContent;
use Swis\McpClient\Schema\Content\TextContent;
use Swis\McpClient\Schema\Resource\BlobResourceContents;
use Swis\McpClient\Schema\Resource\TextResourceContents;
use Swis\McpClient\Schema\Role;

class CallToolResultTest extends TestCase
{
    /**
     * Test creating CallToolResult from array with text content
     */
    public function testFromArrayWithTextContent(): void
    {
        $requestId = 'test-request-123';
        $contentData = [
            [
                'type' => 'text',
                'text' => 'This is tool output text',
            ],
        ];

        $data = [
            'content' => $contentData,
            '_meta' => ['test' => true],
        ];

        $result = CallToolResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertEquals(['test' => true], $result->getMeta());
        $this->assertFalse($result->isError());

        // Test content
        $content = $result->getContent();
        $this->assertCount(1, $content);
        $this->assertInstanceOf(TextContent::class, $content[0]);
        $this->assertEquals('This is tool output text', $content[0]->getText());
    }

    /**
     * Test creating CallToolResult from array with image content
     */
    public function testFromArrayWithImageContent(): void
    {
        $requestId = 'test-request-456';
        $contentData = [
            [
                'type' => 'image',
                'data' => 'base64encodedimagedata',
                'mimeType' => 'image/png',
            ],
        ];

        $data = [
            'content' => $contentData,
            'isError' => true,
        ];

        $result = CallToolResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertNull($result->getMeta());
        $this->assertTrue($result->isError());

        // Test content
        $content = $result->getContent();
        $this->assertCount(1, $content);
        $this->assertInstanceOf(ImageContent::class, $content[0]);
        $this->assertEquals('base64encodedimagedata', $content[0]->getData());
        $this->assertEquals('image/png', $content[0]->getMimeType());
    }

    /**
     * Test creating CallToolResult from array with embedded text resource
     */
    public function testFromArrayWithEmbeddedTextResource(): void
    {
        $requestId = 'test-request-789';
        $contentData = [
            [
                'type' => 'resource',
                'resource' => [
                    'text' => 'Embedded resource text content',
                    'uri' => 'file:///path/to/resource',
                    'mimeType' => 'text/plain',
                ],
            ],
        ];

        $data = [
            'content' => $contentData,
        ];

        $result = CallToolResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertNull($result->getMeta());
        $this->assertFalse($result->isError());

        // Test content
        $content = $result->getContent();
        $this->assertCount(1, $content);
        $this->assertInstanceOf(EmbeddedResource::class, $content[0]);

        $resource = $content[0]->getResource();
        $this->assertInstanceOf(TextResourceContents::class, $resource);
        $this->assertEquals('Embedded resource text content', $resource->getText());
        $this->assertEquals('file:///path/to/resource', $resource->getUri());
        $this->assertEquals('text/plain', $resource->getMimeType());
    }

    /**
     * Test creating CallToolResult from array with embedded blob resource
     */
    public function testFromArrayWithEmbeddedBlobResource(): void
    {
        $requestId = 'test-request-000';
        $contentData = [
            [
                'type' => 'resource',
                'resource' => [
                    'blob' => 'base64encodedblobdata',
                    'uri' => 'file:///path/to/blob',
                    'mimeType' => 'application/octet-stream',
                ],
            ],
        ];

        $data = [
            'content' => $contentData,
        ];

        $result = CallToolResult::fromArray($data, $requestId);

        // Test content
        $content = $result->getContent();
        $this->assertCount(1, $content);
        $this->assertInstanceOf(EmbeddedResource::class, $content[0]);

        $resource = $content[0]->getResource();
        $this->assertInstanceOf(BlobResourceContents::class, $resource);
        $this->assertEquals('base64encodedblobdata', $resource->getBlob());
        $this->assertEquals('file:///path/to/blob', $resource->getUri());
        $this->assertEquals('application/octet-stream', $resource->getMimeType());
    }

    /**
     * Test creating CallToolResult from array with multiple content types
     */
    public function testFromArrayWithMultipleContentTypes(): void
    {
        $requestId = 'test-request-multi';
        $contentData = [
            [
                'type' => 'text',
                'text' => 'Text content',
            ],
            [
                'type' => 'image',
                'data' => 'base64imagedata',
                'mimeType' => 'image/jpeg',
            ],
        ];

        $data = [
            'content' => $contentData,
        ];

        $result = CallToolResult::fromArray($data, $requestId);

        // Test content
        $content = $result->getContent();
        $this->assertCount(2, $content);
        $this->assertInstanceOf(TextContent::class, $content[0]);
        $this->assertInstanceOf(ImageContent::class, $content[1]);
    }

    /**
     * Test toArray method
     */
    public function testToArray(): void
    {
        $requestId = 'test-request-123';
        $textContent = new TextContent('Text content');
        $imageContent = new ImageContent('base64imagedata', 'image/png');

        $result = new CallToolResult($requestId, [$textContent, $imageContent], true, ['test' => true]);

        $array = $result->toArray();

        $this->assertArrayHasKey('content', $array);
        $this->assertCount(2, $array['content']);
        $this->assertEquals('text', $array['content'][0]['type']);
        $this->assertEquals('Text content', $array['content'][0]['text']);
        $this->assertEquals('image', $array['content'][1]['type']);
        $this->assertEquals('base64imagedata', $array['content'][1]['data']);
        $this->assertEquals('image/png', $array['content'][1]['mimeType']);
        $this->assertTrue($array['isError']);

        // Test jsonSerialize includes meta
        $json = $result->jsonSerialize();
        $this->assertArrayHasKey('_meta', $json);
        $this->assertEquals(['test' => true], $json['_meta']);
    }

    /**
     * Test with annotations
     */
    public function testWithAnnotations(): void
    {
        $requestId = 'test-request-annotated';
        $annotation = new Annotation([Role::USER, Role::ASSISTANT], 0.9);
        $textContent = new TextContent('Annotated text', $annotation);

        $result = new CallToolResult($requestId, [$textContent]);

        $array = $result->toArray();
        $this->assertArrayHasKey('annotations', $array['content'][0]);
        $this->assertCount(2, $array['content'][0]['annotations']['audience']);
        $this->assertEquals(0.9, $array['content'][0]['annotations']['priority']);
    }
}
