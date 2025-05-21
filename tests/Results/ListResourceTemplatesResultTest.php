<?php

namespace Swis\McpClient\Tests\Results;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Results\ListResourceTemplatesResult;
use Swis\McpClient\Schema\Annotation;
use Swis\McpClient\Schema\ResourceTemplate;
use Swis\McpClient\Schema\Role;

class ListResourceTemplatesResultTest extends TestCase
{
    /**
     * Test creating ListResourceTemplatesResult from array
     */
    public function testFromArray(): void
    {
        $requestId = 'test-request-123';
        $templateData = [
            'name' => 'testTemplate',
            'uriTemplate' => 'file:///path/to/template/{id}',
            'description' => 'A test template',
            'mimeType' => 'text/plain',
        ];

        $data = [
            'templates' => [$templateData],
            '_meta' => ['test' => true],
        ];

        $result = ListResourceTemplatesResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertEquals(['test' => true], $result->getMeta());
        $this->assertNull($result->getNextCursor());

        // Test templates
        $templates = $result->getTemplates();
        $this->assertCount(1, $templates);
        $this->assertInstanceOf(ResourceTemplate::class, $templates[0]);
        $this->assertEquals('testTemplate', $templates[0]->getName());
        $this->assertEquals('file:///path/to/template/{id}', $templates[0]->getUriTemplate());
        $this->assertEquals('A test template', $templates[0]->getDescription());
        $this->assertEquals('text/plain', $templates[0]->getMimeType());
    }

    /**
     * Test creating ListResourceTemplatesResult from array with annotations and cursor
     */
    public function testFromArrayWithAnnotationsAndCursor(): void
    {
        $requestId = 'test-request-456';
        $templateData = [
            'name' => 'advancedTemplate',
            'uriTemplate' => 'file:///path/to/advanced/{id}',
            'annotations' => [
                'audience' => ['user', 'assistant'],
                'priority' => 0.8,
            ],
        ];

        $data = [
            'templates' => [$templateData],
            'nextCursor' => 'next-page-token',
        ];

        $result = ListResourceTemplatesResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertNull($result->getMeta());
        $this->assertEquals('next-page-token', $result->getNextCursor());

        // Test templates
        $templates = $result->getTemplates();
        $this->assertCount(1, $templates);
        $this->assertInstanceOf(ResourceTemplate::class, $templates[0]);
        $this->assertEquals('advancedTemplate', $templates[0]->getName());

        // Test annotations
        $annotations = $templates[0]->getAnnotations();
        $this->assertInstanceOf(Annotation::class, $annotations);
        $this->assertEquals(0.8, $annotations->getPriority());

        $audience = $annotations->getAudience();
        $this->assertCount(2, $audience);
        $this->assertEquals(Role::USER, $audience[0]);
        $this->assertEquals(Role::ASSISTANT, $audience[1]);
    }

    /**
     * Test creating ListResourceTemplatesResult from array with multiple templates
     */
    public function testFromArrayWithMultipleTemplates(): void
    {
        $requestId = 'test-request-789';
        $template1Data = [
            'name' => 'template1',
            'uriTemplate' => 'file:///path/to/template1/{id}',
        ];

        $template2Data = [
            'name' => 'template2',
            'uriTemplate' => 'file:///path/to/template2/{id}',
        ];

        $data = [
            'templates' => [$template1Data, $template2Data],
        ];

        $result = ListResourceTemplatesResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertNull($result->getMeta());

        // Test templates
        $templates = $result->getTemplates();
        $this->assertCount(2, $templates);
        $this->assertEquals('template1', $templates[0]->getName());
        $this->assertEquals('template2', $templates[1]->getName());
        $this->assertEquals('file:///path/to/template1/{id}', $templates[0]->getUriTemplate());
        $this->assertEquals('file:///path/to/template2/{id}', $templates[1]->getUriTemplate());
    }

    /**
     * Test toArray method
     */
    public function testToArray(): void
    {
        $requestId = 'test-request-123';
        $template = new ResourceTemplate(
            'testTemplate',
            'file:///path/to/template/{id}',
            'Test template',
            'text/plain'
        );

        $result = new ListResourceTemplatesResult($requestId, [$template], 'next-token', ['test' => true]);

        $array = $result->toArray();

        $this->assertArrayHasKey('templates', $array);
        $this->assertCount(1, $array['templates']);
        $this->assertEquals('testTemplate', $array['templates'][0]['name']);
        $this->assertEquals('file:///path/to/template/{id}', $array['templates'][0]['uriTemplate']);
        $this->assertEquals('Test template', $array['templates'][0]['description']);
        $this->assertEquals('text/plain', $array['templates'][0]['mimeType']);
        $this->assertEquals('next-token', $array['nextCursor']);

        // Test jsonSerialize includes meta
        $json = $result->jsonSerialize();
        $this->assertArrayHasKey('_meta', $json);
        $this->assertEquals(['test' => true], $json['_meta']);
    }
}
