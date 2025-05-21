<?php

namespace Swis\McpClient\Tests\Results;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Results\ListResourcesResult;
use Swis\McpClient\Schema\Annotation;
use Swis\McpClient\Schema\Resource;
use Swis\McpClient\Schema\Role;

class ListResourcesResultTest extends TestCase
{
    /**
     * Test creating ListResourcesResult from array
     */
    public function testFromArray(): void
    {
        $requestId = 'test-request-123';
        $resourceData = [
            'name' => 'testResource',
            'uri' => 'file:///path/to/resource',
            'description' => 'A test resource',
            'mimeType' => 'text/plain',
            'size' => 1024,
        ];

        $data = [
            'resources' => [$resourceData],
            '_meta' => ['test' => true],
        ];

        $result = ListResourcesResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertEquals(['test' => true], $result->getMeta());
        $this->assertNull($result->getNextCursor());

        // Test resources
        $resources = $result->getResources();
        $this->assertCount(1, $resources);
        $this->assertInstanceOf(Resource::class, $resources[0]);
        $this->assertEquals('testResource', $resources[0]->getName());
        $this->assertEquals('file:///path/to/resource', $resources[0]->getUri());
        $this->assertEquals('A test resource', $resources[0]->getDescription());
        $this->assertEquals('text/plain', $resources[0]->getMimeType());
        $this->assertEquals(1024, $resources[0]->getSize());
    }

    /**
     * Test creating ListResourcesResult from array with annotations and cursor
     */
    public function testFromArrayWithAnnotationsAndCursor(): void
    {
        $requestId = 'test-request-456';
        $resourceData = [
            'name' => 'advancedResource',
            'uri' => 'file:///path/to/advanced',
            'annotations' => [
                'audience' => ['user', 'assistant'],
                'priority' => 0.8,
            ],
        ];

        $data = [
            'resources' => [$resourceData],
            'nextCursor' => 'next-page-token',
        ];

        $result = ListResourcesResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertNull($result->getMeta());
        $this->assertEquals('next-page-token', $result->getNextCursor());

        // Test resources
        $resources = $result->getResources();
        $this->assertCount(1, $resources);
        $this->assertInstanceOf(Resource::class, $resources[0]);
        $this->assertEquals('advancedResource', $resources[0]->getName());

        // Test annotations
        $annotations = $resources[0]->getAnnotations();
        $this->assertInstanceOf(Annotation::class, $annotations);
        $this->assertEquals(0.8, $annotations->getPriority());

        $audience = $annotations->getAudience();
        $this->assertCount(2, $audience);
        $this->assertEquals(Role::USER, $audience[0]);
        $this->assertEquals(Role::ASSISTANT, $audience[1]);
    }

    /**
     * Test creating ListResourcesResult from array with multiple resources
     */
    public function testFromArrayWithMultipleResources(): void
    {
        $requestId = 'test-request-789';
        $resource1Data = [
            'name' => 'resource1',
            'uri' => 'file:///path/to/resource1',
        ];

        $resource2Data = [
            'name' => 'resource2',
            'uri' => 'file:///path/to/resource2',
        ];

        $data = [
            'resources' => [$resource1Data, $resource2Data],
        ];

        $result = ListResourcesResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertNull($result->getMeta());

        // Test resources
        $resources = $result->getResources();
        $this->assertCount(2, $resources);
        $this->assertEquals('resource1', $resources[0]->getName());
        $this->assertEquals('resource2', $resources[1]->getName());
        $this->assertEquals('file:///path/to/resource1', $resources[0]->getUri());
        $this->assertEquals('file:///path/to/resource2', $resources[1]->getUri());
    }

    /**
     * Test toArray method
     */
    public function testToArray(): void
    {
        $requestId = 'test-request-123';
        $resource = new Resource(
            'testResource',
            'file:///path/to/resource',
            'Test resource',
            'text/plain',
            1024
        );

        $result = new ListResourcesResult($requestId, [$resource], 'next-token', ['test' => true]);

        $array = $result->toArray();

        $this->assertArrayHasKey('resources', $array);
        $this->assertCount(1, $array['resources']);
        $this->assertEquals('testResource', $array['resources'][0]['name']);
        $this->assertEquals('file:///path/to/resource', $array['resources'][0]['uri']);
        $this->assertEquals('Test resource', $array['resources'][0]['description']);
        $this->assertEquals('text/plain', $array['resources'][0]['mimeType']);
        $this->assertEquals(1024, $array['resources'][0]['size']);
        $this->assertEquals('next-token', $array['nextCursor']);

        // Test jsonSerialize includes meta
        $json = $result->jsonSerialize();
        $this->assertArrayHasKey('_meta', $json);
        $this->assertEquals(['test' => true], $json['_meta']);
    }
}
