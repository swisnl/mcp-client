<?php

namespace Swis\McpClient\Tests\Results;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Exceptions\UnknownResourceContentTypeException;
use Swis\McpClient\Results\ReadResourceResult;
use Swis\McpClient\Schema\Resource\BlobResourceContents;
use Swis\McpClient\Schema\Resource\TextResourceContents;

class ReadResourceResultTest extends TestCase
{
    /**
     * Test creating ReadResourceResult from array with text content
     */
    public function testFromArrayWithTextContent(): void
    {
        $requestId = 'test-request-123';
        $contentsData = [
            [
                'text' => 'This is text resource content',
                'uri' => 'file:///path/to/text',
                'mimeType' => 'text/plain',
            ],
        ];

        $data = [
            'contents' => $contentsData,
            '_meta' => ['test' => true],
        ];

        $result = ReadResourceResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertEquals(['test' => true], $result->getMeta());

        // Test contents
        $contents = $result->getContents();
        $this->assertCount(1, $contents);
        $this->assertInstanceOf(TextResourceContents::class, $contents[0]);
        $this->assertEquals('This is text resource content', $contents[0]->getText());
        $this->assertEquals('file:///path/to/text', $contents[0]->getUri());
        $this->assertEquals('text/plain', $contents[0]->getMimeType());
    }

    /**
     * Test creating ReadResourceResult from array with blob content
     */
    public function testFromArrayWithBlobContent(): void
    {
        $requestId = 'test-request-456';
        $contentsData = [
            [
                'blob' => 'base64encodeddata',
                'uri' => 'file:///path/to/blob',
                'mimeType' => 'application/octet-stream',
            ],
        ];

        $data = [
            'contents' => $contentsData,
        ];

        $result = ReadResourceResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertNull($result->getMeta());

        // Test contents
        $contents = $result->getContents();
        $this->assertCount(1, $contents);
        $this->assertInstanceOf(BlobResourceContents::class, $contents[0]);
        $this->assertEquals('base64encodeddata', $contents[0]->getBlob());
        $this->assertEquals('file:///path/to/blob', $contents[0]->getUri());
        $this->assertEquals('application/octet-stream', $contents[0]->getMimeType());
    }

    /**
     * Test creating ReadResourceResult from array with multiple contents
     */
    public function testFromArrayWithMultipleContents(): void
    {
        $requestId = 'test-request-789';
        $contentsData = [
            [
                'text' => 'Text content',
                'uri' => 'file:///path/to/text',
            ],
            [
                'blob' => 'base64data',
                'uri' => 'file:///path/to/blob',
            ],
        ];

        $data = [
            'contents' => $contentsData,
        ];

        $result = ReadResourceResult::fromArray($data, $requestId);

        // Test contents
        $contents = $result->getContents();
        $this->assertCount(2, $contents);
        $this->assertInstanceOf(TextResourceContents::class, $contents[0]);
        $this->assertInstanceOf(BlobResourceContents::class, $contents[1]);
        $this->assertEquals('Text content', $contents[0]->getText());
        $this->assertEquals('base64data', $contents[1]->getBlob());
    }

    /**
     * Test creating ReadResourceResult with invalid content type
     */
    public function testFromArrayWithInvalidContentType(): void
    {
        $requestId = 'test-request-invalid';
        $contentsData = [
            [
                'invalid' => 'This is not a valid content type',
                'uri' => 'file:///path/to/invalid',
            ],
        ];

        $data = [
            'contents' => $contentsData,
        ];

        $this->expectException(UnknownResourceContentTypeException::class);
        ReadResourceResult::fromArray($data, $requestId);
    }

    /**
     * Test toArray method
     */
    public function testToArray(): void
    {
        $requestId = 'test-request-000';
        $textContent = new TextResourceContents('Text content', 'file:///path/to/text', 'text/plain');
        $blobContent = new BlobResourceContents('base64data', 'file:///path/to/blob', 'application/octet-stream');

        $result = new ReadResourceResult($requestId, [$textContent, $blobContent], ['test' => true]);

        $array = $result->toArray();

        $this->assertArrayHasKey('contents', $array);
        $this->assertCount(2, $array['contents']);

        // Check text content
        $this->assertEquals('Text content', $array['contents'][0]['text']);
        $this->assertEquals('file:///path/to/text', $array['contents'][0]['uri']);
        $this->assertEquals('text/plain', $array['contents'][0]['mimeType']);

        // Check blob content
        $this->assertEquals('base64data', $array['contents'][1]['blob']);
        $this->assertEquals('file:///path/to/blob', $array['contents'][1]['uri']);
        $this->assertEquals('application/octet-stream', $array['contents'][1]['mimeType']);

        // Test jsonSerialize includes meta
        $json = $result->jsonSerialize();
        $this->assertArrayHasKey('_meta', $json);
        $this->assertEquals(['test' => true], $json['_meta']);
    }
}
