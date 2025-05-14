<?php

namespace Swis\McpClient\Tests\Schema\Resource;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Schema\Resource\BlobResourceContents;

class BlobResourceContentsTest extends TestCase
{
    /**
     * Test creating a BlobResourceContents instance with all parameters
     */
    public function testBlobResourceContentsConstructionWithAllParameters(): void
    {
        $content = new BlobResourceContents(
            blob: 'base64encodedblob==',
            uri: 'file:///path/to/resource.bin',
            mimeType: 'application/octet-stream'
        );

        $this->assertEquals('base64encodedblob==', $content->getBlob());
        $this->assertEquals('file:///path/to/resource.bin', $content->getUri());
        $this->assertEquals('application/octet-stream', $content->getMimeType());
    }

    /**
     * Test creating a BlobResourceContents instance with minimal parameters
     */
    public function testBlobResourceContentsConstructionWithMinimalParameters(): void
    {
        $content = new BlobResourceContents(
            blob: 'base64encodedblob==',
            uri: 'file:///path/to/resource.bin'
        );

        $this->assertEquals('base64encodedblob==', $content->getBlob());
        $this->assertEquals('file:///path/to/resource.bin', $content->getUri());
        $this->assertNull($content->getMimeType());
    }

    /**
     * Test converting BlobResourceContents to array with all parameters
     */
    public function testBlobResourceContentsToArrayWithAllParameters(): void
    {
        $content = new BlobResourceContents(
            blob: 'base64encodedblob==',
            uri: 'file:///path/to/resource.bin',
            mimeType: 'application/octet-stream'
        );

        $array = $content->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('blob', $array);
        $this->assertArrayHasKey('uri', $array);
        $this->assertArrayHasKey('mimeType', $array);

        $this->assertEquals('base64encodedblob==', $array['blob']);
        $this->assertEquals('file:///path/to/resource.bin', $array['uri']);
        $this->assertEquals('application/octet-stream', $array['mimeType']);
    }

    /**
     * Test converting BlobResourceContents to array with minimal parameters
     */
    public function testBlobResourceContentsToArrayWithMinimalParameters(): void
    {
        $content = new BlobResourceContents(
            blob: 'base64encodedblob==',
            uri: 'file:///path/to/resource.bin'
        );

        $array = $content->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('blob', $array);
        $this->assertArrayHasKey('uri', $array);
        $this->assertArrayNotHasKey('mimeType', $array);

        $this->assertEquals('base64encodedblob==', $array['blob']);
        $this->assertEquals('file:///path/to/resource.bin', $array['uri']);
    }

    /**
     * Test creating BlobResourceContents from array with all parameters
     */
    public function testBlobResourceContentsFromArrayWithAllParameters(): void
    {
        $array = [
            'blob' => 'base64encodedblob==',
            'uri' => 'file:///path/to/resource.bin',
            'mimeType' => 'application/octet-stream',
        ];

        $content = BlobResourceContents::fromArray($array);

        $this->assertEquals('base64encodedblob==', $content->getBlob());
        $this->assertEquals('file:///path/to/resource.bin', $content->getUri());
        $this->assertEquals('application/octet-stream', $content->getMimeType());
    }

    /**
     * Test creating BlobResourceContents from array with minimal parameters
     */
    public function testBlobResourceContentsFromArrayWithMinimalParameters(): void
    {
        $array = [
            'blob' => 'base64encodedblob==',
            'uri' => 'file:///path/to/resource.bin',
        ];

        $content = BlobResourceContents::fromArray($array);

        $this->assertEquals('base64encodedblob==', $content->getBlob());
        $this->assertEquals('file:///path/to/resource.bin', $content->getUri());
        $this->assertNull($content->getMimeType());
    }

    /**
     * Test symmetry between toArray() and fromArray()
     */
    public function testSymmetryBetweenToArrayAndFromArray(): void
    {
        $original = new BlobResourceContents(
            blob: 'base64encodedblob==',
            uri: 'file:///path/to/resource.bin',
            mimeType: 'application/octet-stream'
        );

        $array = $original->toArray();
        $recreated = BlobResourceContents::fromArray($array);

        $this->assertEquals($original->getBlob(), $recreated->getBlob());
        $this->assertEquals($original->getUri(), $recreated->getUri());
        $this->assertEquals($original->getMimeType(), $recreated->getMimeType());
    }
}
