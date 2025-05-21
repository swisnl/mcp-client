<?php

namespace Swis\McpClient\Tests\Results;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Results\CompleteResult;

class CompleteResultTest extends TestCase
{
    /**
     * Test creating CompleteResult from array with full data
     */
    public function testFromArrayWithFullData(): void
    {
        $requestId = 'test-request-123';
        $completionData = [
            'values' => ['completion1', 'completion2', 'completion3'],
            'total' => 3,
            'hasMore' => false,
        ];

        $data = [
            'completion' => $completionData,
            '_meta' => ['test' => true],
        ];

        $result = CompleteResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertEquals(['test' => true], $result->getMeta());

        // Test completion properties
        $this->assertEquals($completionData, $result->getCompletion());
        $this->assertEquals(['completion1', 'completion2', 'completion3'], $result->getValues());
        $this->assertEquals(3, $result->getTotal());
        $this->assertFalse($result->hasMore());
    }

    /**
     * Test creating CompleteResult from array with partial data
     */
    public function testFromArrayWithPartialData(): void
    {
        $requestId = 'test-request-456';
        $completionData = [
            'values' => ['completion1'],
            'hasMore' => true,
        ];

        $data = [
            'completion' => $completionData,
        ];

        $result = CompleteResult::fromArray($data, $requestId);

        // Test basic properties
        $this->assertEquals($requestId, $result->getRequestId());
        $this->assertNull($result->getMeta());

        // Test completion properties
        $this->assertEquals($completionData, $result->getCompletion());
        $this->assertEquals(['completion1'], $result->getValues());
        $this->assertEquals(1, $result->getTotal()); // Should calculate from values length
        $this->assertTrue($result->hasMore());
    }

    /**
     * Test creating CompleteResult from array with minimal data
     */
    public function testFromArrayWithMinimalData(): void
    {
        $requestId = 'test-request-789';
        $completionData = [];

        $data = [
            'completion' => $completionData,
        ];

        $result = CompleteResult::fromArray($data, $requestId);

        // Test completion properties
        $this->assertEquals($completionData, $result->getCompletion());
        $this->assertEquals([], $result->getValues());
        $this->assertEquals(0, $result->getTotal());
        $this->assertFalse($result->hasMore());
    }

    /**
     * Test toArray method
     */
    public function testToArray(): void
    {
        $requestId = 'test-request-123';
        $completion = [
            'values' => ['completion1', 'completion2'],
            'total' => 2,
            'hasMore' => false,
        ];

        $result = new CompleteResult($requestId, $completion, ['test' => true]);

        $array = $result->toArray();

        $this->assertArrayHasKey('completion', $array);
        $this->assertEquals($completion, $array['completion']);

        // Test jsonSerialize includes meta
        $json = $result->jsonSerialize();
        $this->assertArrayHasKey('_meta', $json);
        $this->assertEquals(['test' => true], $json['_meta']);
    }

    /**
     * Test getters with different data scenarios
     */
    public function testGettersWithDifferentDataScenarios(): void
    {
        // 1. Test with only values
        $completion1 = [
            'values' => ['text1', 'text2'],
        ];
        $result1 = new CompleteResult('req1', $completion1);
        $this->assertEquals(['text1', 'text2'], $result1->getValues());
        $this->assertEquals(2, $result1->getTotal());
        $this->assertFalse($result1->hasMore());

        // 2. Test with only total
        $completion2 = [
            'total' => 5,
        ];
        $result2 = new CompleteResult('req2', $completion2);
        $this->assertEquals([], $result2->getValues());
        $this->assertEquals(5, $result2->getTotal());
        $this->assertFalse($result2->hasMore());

        // 3. Test with only hasMore
        $completion3 = [
            'hasMore' => true,
        ];
        $result3 = new CompleteResult('req3', $completion3);
        $this->assertEquals([], $result3->getValues());
        $this->assertEquals(0, $result3->getTotal());
        $this->assertTrue($result3->hasMore());
    }
}
