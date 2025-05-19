<?php

namespace Swis\McpClient\Tests\Schema;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Exceptions\UnknownContentTypeException;
use Swis\McpClient\Schema\Annotation;
use Swis\McpClient\Schema\Content\EmbeddedResource;
use Swis\McpClient\Schema\Content\ImageContent;
use Swis\McpClient\Schema\Content\TextContent;
use Swis\McpClient\Schema\PromptMessage;
use Swis\McpClient\Schema\Resource\TextResourceContents;
use Swis\McpClient\Schema\Role;

class PromptMessageTest extends TestCase
{
    /**
     * Test creating a PromptMessage instance with TextContent
     */
    public function testPromptMessageConstructionWithTextContent(): void
    {
        $textContent = new TextContent(
            text: 'This is test text content'
        );

        $promptMessage = new PromptMessage(
            role: Role::USER,
            content: $textContent
        );

        $this->assertEquals(Role::USER, $promptMessage->getRole());
        $this->assertSame($textContent, $promptMessage->getUri());
    }

    /**
     * Test creating a PromptMessage instance with ImageContent
     */
    public function testPromptMessageConstructionWithImageContent(): void
    {
        $imageContent = new ImageContent(
            data: 'base64_encoded_data',
            mimeType: 'image/png'
        );

        $promptMessage = new PromptMessage(
            role: Role::ASSISTANT,
            content: $imageContent
        );

        $this->assertEquals(Role::ASSISTANT, $promptMessage->getRole());
        $this->assertSame($imageContent, $promptMessage->getUri());
    }

    /**
     * Test creating a PromptMessage instance with EmbeddedResource
     */
    public function testPromptMessageConstructionWithEmbeddedResource(): void
    {
        $resourceContent = new TextResourceContents(
            text: 'This is embedded resource text',
            uri: 'resource:123'
        );

        $embeddedResource = new EmbeddedResource(
            resource: $resourceContent
        );

        $promptMessage = new PromptMessage(
            role: Role::USER,
            content: $embeddedResource
        );

        $this->assertEquals(Role::USER, $promptMessage->getRole());
        $this->assertSame($embeddedResource, $promptMessage->getUri());
    }

    /**
     * Test converting PromptMessage with TextContent to array
     */
    public function testPromptMessageToArrayWithTextContent(): void
    {
        $textContent = new TextContent(
            text: 'This is test text content'
        );

        $promptMessage = new PromptMessage(
            role: Role::USER,
            content: $textContent
        );

        $array = $promptMessage->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('role', $array);
        $this->assertArrayHasKey('content', $array);

        $this->assertEquals('user', $array['role']);
        $this->assertEquals($textContent->toArray(), $array['content']);
    }

    /**
     * Test converting PromptMessage with ImageContent to array
     */
    public function testPromptMessageToArrayWithImageContent(): void
    {
        $imageContent = new ImageContent(
            data: 'base64_encoded_data',
            mimeType: 'image/png'
        );

        $promptMessage = new PromptMessage(
            role: Role::ASSISTANT,
            content: $imageContent
        );

        $array = $promptMessage->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('role', $array);
        $this->assertArrayHasKey('content', $array);

        $this->assertEquals('assistant', $array['role']);
        $this->assertEquals($imageContent->toArray(), $array['content']);
    }

    /**
     * Test creating PromptMessage from array with TextContent
     */
    public function testPromptMessageFromArrayWithTextContent(): void
    {
        $array = [
            'role' => 'user',
            'content' => [
                'type' => 'text',
                'text' => 'This is test text content',
            ],
        ];

        $promptMessage = PromptMessage::fromArray($array);

        $this->assertEquals(Role::USER, $promptMessage->getRole());
        $content = $promptMessage->getUri();
        $this->assertInstanceOf(TextContent::class, $content);
        $this->assertEquals('This is test text content', $content->getText());
    }

    /**
     * Test creating PromptMessage from array with ImageContent
     */
    public function testPromptMessageFromArrayWithImageContent(): void
    {
        $array = [
            'role' => 'assistant',
            'content' => [
                'type' => 'image',
                'data' => 'base64_encoded_data',
                'mimeType' => 'image/png',
            ],
        ];

        $promptMessage = PromptMessage::fromArray($array);

        $this->assertEquals(Role::ASSISTANT, $promptMessage->getRole());
        $content = $promptMessage->getUri();
        $this->assertInstanceOf(ImageContent::class, $content);
        $this->assertEquals('base64_encoded_data', $content->getData());
        $this->assertEquals('image/png', $content->getMimeType());
    }

    /**
     * Test creating PromptMessage from array with unknown content type
     */
    public function testPromptMessageFromArrayWithUnknownContentType(): void
    {
        $array = [
            'role' => 'user',
            'content' => [
                'type' => 'unknown',
                'text' => 'This is test text content',
            ],
        ];

        $this->expectException(UnknownContentTypeException::class);
        PromptMessage::fromArray($array);
    }

    /**
     * Test symmetry between toArray() and fromArray()
     */
    public function testSymmetryBetweenToArrayAndFromArray(): void
    {
        $textContent = new TextContent(
            text: 'This is test text content',
            annotations: new Annotation(
                audience: [Role::ASSISTANT],
                priority: 0.5
            )
        );

        $original = new PromptMessage(
            role: Role::USER,
            content: $textContent
        );

        $array = $original->toArray();
        $recreated = PromptMessage::fromArray($array);

        $this->assertEquals($original->getRole(), $recreated->getRole());

        $originalContent = $original->getUri();
        $recreatedContent = $recreated->getUri();

        $this->assertInstanceOf(TextContent::class, $recreatedContent);
        if ($originalContent instanceof TextContent && $recreatedContent instanceof TextContent) {
            $this->assertEquals($originalContent->getText(), $recreatedContent->getText());
        }
    }
}
