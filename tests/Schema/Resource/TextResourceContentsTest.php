<?php

namespace Swis\McpClient\Tests\Schema\Resource;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Schema\Resource\TextResourceContents;

class TextResourceContentsTest extends TestCase
{
    /**
     * Test creating a TextResourceContents instance with all parameters
     */
    public function testTextResourceContentsConstructionWithAllParameters(): void
    {
        $content = new TextResourceContents(
            text: 'This is text resource content',
            uri: 'file:///path/to/resource.txt',
            mimeType: 'text/plain'
        );

        $this->assertEquals('This is text resource content', $content->getText());
        $this->assertEquals('file:///path/to/resource.txt', $content->getUri());
        $this->assertEquals('text/plain', $content->getMimeType());
    }

    /**
     * Test creating a TextResourceContents instance with minimal parameters
     */
    public function testTextResourceContentsConstructionWithMinimalParameters(): void
    {
        $content = new TextResourceContents(
            text: 'This is text resource content',
            uri: 'file:///path/to/resource.txt'
        );

        $this->assertEquals('This is text resource content', $content->getText());
        $this->assertEquals('file:///path/to/resource.txt', $content->getUri());
        $this->assertNull($content->getMimeType());
    }

    /**
     * Test converting TextResourceContents to array with all parameters
     */
    public function testTextResourceContentsToArrayWithAllParameters(): void
    {
        $content = new TextResourceContents(
            text: 'This is text resource content',
            uri: 'file:///path/to/resource.txt',
            mimeType: 'text/plain'
        );

        $array = $content->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('text', $array);
        $this->assertArrayHasKey('uri', $array);
        $this->assertArrayHasKey('mimeType', $array);

        $this->assertEquals('This is text resource content', $array['text']);
        $this->assertEquals('file:///path/to/resource.txt', $array['uri']);
        $this->assertEquals('text/plain', $array['mimeType']);
    }

    /**
     * Test converting TextResourceContents to array with minimal parameters
     */
    public function testTextResourceContentsToArrayWithMinimalParameters(): void
    {
        $content = new TextResourceContents(
            text: 'This is text resource content',
            uri: 'file:///path/to/resource.txt'
        );

        $array = $content->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('text', $array);
        $this->assertArrayHasKey('uri', $array);
        $this->assertArrayNotHasKey('mimeType', $array);

        $this->assertEquals('This is text resource content', $array['text']);
        $this->assertEquals('file:///path/to/resource.txt', $array['uri']);
    }

    /**
     * Test creating TextResourceContents from array with all parameters
     */
    public function testTextResourceContentsFromArrayWithAllParameters(): void
    {
        $array = [
            'text' => 'This is text resource content',
            'uri' => 'file:///path/to/resource.txt',
            'mimeType' => 'text/plain',
        ];

        $content = TextResourceContents::fromArray($array);

        $this->assertEquals('This is text resource content', $content->getText());
        $this->assertEquals('file:///path/to/resource.txt', $content->getUri());
        $this->assertEquals('text/plain', $content->getMimeType());
    }

    /**
     * Test creating TextResourceContents from array with minimal parameters
     */
    public function testTextResourceContentsFromArrayWithMinimalParameters(): void
    {
        $array = [
            'text' => 'This is text resource content',
            'uri' => 'file:///path/to/resource.txt',
        ];

        $content = TextResourceContents::fromArray($array);

        $this->assertEquals('This is text resource content', $content->getText());
        $this->assertEquals('file:///path/to/resource.txt', $content->getUri());
        $this->assertNull($content->getMimeType());
    }

    /**
     * Test symmetry between toArray() and fromArray()
     */
    public function testSymmetryBetweenToArrayAndFromArray(): void
    {
        $original = new TextResourceContents(
            text: 'This is text resource content',
            uri: 'file:///path/to/resource.txt',
            mimeType: 'text/plain'
        );

        $array = $original->toArray();
        $recreated = TextResourceContents::fromArray($array);

        $this->assertEquals($original->getText(), $recreated->getText());
        $this->assertEquals($original->getUri(), $recreated->getUri());
        $this->assertEquals($original->getMimeType(), $recreated->getMimeType());
    }
}
