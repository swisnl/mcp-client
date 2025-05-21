<?php

namespace Swis\McpClient\Tests\Results;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Results\InitializeResult;

class InitializeResultTest extends TestCase
{
    /**
     * Test creating InitializeResult from array with full data
     */
    public function testFromArrayWithFullData(): void
    {
        $requestId = 'test-request-123';
        $capabilities = [
            'completions' => [],
            'resources' => ['subscribe' => true],
            'tools' => ['listChanged' => false],
        ];
        $protocolVersion = '1.0';
        $serverInfo = [
            'name' => 'TestServer',
            'version' => '2.0.0',
        ];
        $instructions = 'Test server';

        $data = [
            'capabilities' => $capabilities,
            'protocolVersion' => $protocolVersion,
            'serverInfo' => $serverInfo,
            'instructions' => $instructions,
            '_meta' => ['test' => true],
        ];

        $result = InitializeResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertEquals(['test' => true], $result->getMeta());

        // Test specific properties
        $this->assertEquals($capabilities, $result->getCapabilities());
        $this->assertEquals($protocolVersion, $result->getProtocolVersion());
        $this->assertEquals($serverInfo, $result->getServerInfo());
        $this->assertEquals($instructions, $result->getInstructions());
    }

    /**
     * Test creating InitializeResult from array without optional fields
     */
    public function testFromArrayWithoutOptionalFields(): void
    {
        $requestId = 'test-request-456';
        $capabilities = [];
        $protocolVersion = '1.0';
        $serverInfo = [
            'name' => 'MinimalServer',
            'version' => '0.1',
        ];

        $data = [
            'capabilities' => $capabilities,
            'protocolVersion' => $protocolVersion,
            'serverInfo' => $serverInfo,
        ];

        $result = InitializeResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertNull($result->getMeta());

        // Test specific properties
        $this->assertEquals($capabilities, $result->getCapabilities());
        $this->assertEquals($protocolVersion, $result->getProtocolVersion());
        $this->assertEquals($serverInfo, $result->getServerInfo());
        $this->assertNull($result->getInstructions());
    }

    /**
     * Test toArray method
     */
    public function testToArray(): void
    {
        $requestId = 'test-request-789';
        $capabilities = ['tools' => ['listChanged' => true]];
        $protocolVersion = '1.0';
        $serverInfo = [
            'name' => 'TestServer',
            'version' => '1.0.0',
        ];
        $instructions = 'Test server';

        $result = new InitializeResult(
            $requestId,
            $capabilities,
            $protocolVersion,
            $serverInfo,
            $instructions,
            ['test' => true]
        );

        $array = $result->toArray();

        $this->assertArrayHasKey('capabilities', $array);
        $this->assertArrayHasKey('protocolVersion', $array);
        $this->assertArrayHasKey('serverInfo', $array);
        $this->assertArrayHasKey('instructions', $array);

        $this->assertEquals($capabilities, $array['capabilities']);
        $this->assertEquals($protocolVersion, $array['protocolVersion']);
        $this->assertEquals($serverInfo, $array['serverInfo']);
        $this->assertEquals($instructions, $array['instructions']);

        // Test jsonSerialize includes meta
        $json = $result->jsonSerialize();
        $this->assertArrayHasKey('_meta', $json);
        $this->assertEquals(['test' => true], $json['_meta']);
    }

    /**
     * Test toArray method without instructions
     */
    public function testToArrayWithoutInstructions(): void
    {
        $requestId = 'test-request-000';
        $capabilities = ['complete' => '1.0'];
        $protocolVersion = '1.0';
        $serverInfo = [
            'name' => 'TestServer',
            'version' => '1.0.0',
        ];

        $result = new InitializeResult(
            $requestId,
            $capabilities,
            $protocolVersion,
            $serverInfo
        );

        $array = $result->toArray();

        $this->assertArrayHasKey('capabilities', $array);
        $this->assertArrayHasKey('protocolVersion', $array);
        $this->assertArrayHasKey('serverInfo', $array);
        $this->assertArrayNotHasKey('instructions', $array);

        // Test jsonSerialize does not include meta
        $json = $result->jsonSerialize();
        $this->assertArrayNotHasKey('_meta', $json);
    }
}
