<?php

namespace Swis\McpClient\Tests\Schema;

use PHPUnit\Framework\TestCase;
use Swis\McpClient\Schema\Annotation;
use Swis\McpClient\Schema\Role;

class AnnotationTest extends TestCase
{
    /**
     * Test creating an Annotation instance with all parameters
     */
    public function testAnnotationConstructionWithAllParameters(): void
    {
        $audience = [Role::ASSISTANT, Role::USER];
        $priority = 0.8;

        $annotation = new Annotation(
            audience: $audience,
            priority: $priority
        );

        $this->assertEquals($audience, $annotation->getAudience());
        $this->assertEquals($priority, $annotation->getPriority());
    }

    /**
     * Test creating an Annotation instance with only audience
     */
    public function testAnnotationConstructionWithOnlyAudience(): void
    {
        $audience = [Role::ASSISTANT];

        $annotation = new Annotation(
            audience: $audience
        );

        $this->assertEquals($audience, $annotation->getAudience());
        $this->assertNull($annotation->getPriority());
    }

    /**
     * Test creating an Annotation instance with only priority
     */
    public function testAnnotationConstructionWithOnlyPriority(): void
    {
        $priority = 0.5;

        $annotation = new Annotation(
            priority: $priority
        );

        $this->assertNull($annotation->getAudience());
        $this->assertEquals($priority, $annotation->getPriority());
    }

    /**
     * Test creating an Annotation instance with no parameters
     */
    public function testAnnotationConstructionWithNoParameters(): void
    {
        $annotation = new Annotation();

        $this->assertNull($annotation->getAudience());
        $this->assertNull($annotation->getPriority());
    }

    /**
     * Test converting Annotation to array with all parameters
     */
    public function testAnnotationToArrayWithAllParameters(): void
    {
        $audience = [Role::ASSISTANT, Role::USER];
        $priority = 0.8;

        $annotation = new Annotation(
            audience: $audience,
            priority: $priority
        );

        $array = $annotation->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('audience', $array);
        $this->assertArrayHasKey('priority', $array);

        $this->assertEquals(['assistant', 'user'], $array['audience']);
        $this->assertEquals($priority, $array['priority']);
    }

    /**
     * Test converting Annotation to array with only audience
     */
    public function testAnnotationToArrayWithOnlyAudience(): void
    {
        $audience = [Role::ASSISTANT];

        $annotation = new Annotation(
            audience: $audience
        );

        $array = $annotation->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('audience', $array);
        $this->assertArrayNotHasKey('priority', $array);

        $this->assertEquals(['assistant'], $array['audience']);
    }

    /**
     * Test converting Annotation to array with only priority
     */
    public function testAnnotationToArrayWithOnlyPriority(): void
    {
        $priority = 0.5;

        $annotation = new Annotation(
            priority: $priority
        );

        $array = $annotation->toArray();

        $this->assertIsArray($array);
        $this->assertArrayNotHasKey('audience', $array);
        $this->assertArrayHasKey('priority', $array);

        $this->assertEquals($priority, $array['priority']);
    }

    /**
     * Test converting empty Annotation to array
     */
    public function testEmptyAnnotationToArray(): void
    {
        $annotation = new Annotation();

        $array = $annotation->toArray();

        $this->assertIsArray($array);
        $this->assertEmpty($array);
    }

    /**
     * Test creating Annotation from array with all parameters
     */
    public function testAnnotationFromArrayWithAllParameters(): void
    {
        $array = [
            'audience' => ['assistant', 'user'],
            'priority' => 0.8,
        ];

        $annotation = Annotation::fromArray($array);

        $audience = $annotation->getAudience();
        $this->assertNotNull($audience);
        $this->assertCount(2, $audience);
        $this->assertEquals(Role::ASSISTANT, $audience[0]);
        $this->assertEquals(Role::USER, $audience[1]);

        $this->assertEquals(0.8, $annotation->getPriority());
    }

    /**
     * Test creating Annotation from array with only audience
     */
    public function testAnnotationFromArrayWithOnlyAudience(): void
    {
        $array = [
            'audience' => ['assistant'],
        ];

        $annotation = Annotation::fromArray($array);

        $audience = $annotation->getAudience();
        $this->assertNotNull($audience);
        $this->assertCount(1, $audience);
        $this->assertEquals(Role::ASSISTANT, $audience[0]);

        $this->assertNull($annotation->getPriority());
    }

    /**
     * Test creating Annotation from array with only priority
     */
    public function testAnnotationFromArrayWithOnlyPriority(): void
    {
        $array = [
            'priority' => 0.5,
        ];

        $annotation = Annotation::fromArray($array);

        $this->assertNull($annotation->getAudience());
        $this->assertEquals(0.5, $annotation->getPriority());
    }

    /**
     * Test creating Annotation from empty array
     */
    public function testAnnotationFromEmptyArray(): void
    {
        $array = [];

        $annotation = Annotation::fromArray($array);

        $this->assertNull($annotation->getAudience());
        $this->assertNull($annotation->getPriority());
    }

    /**
     * Test symmetry between toArray() and fromArray()
     */
    public function testSymmetryBetweenToArrayAndFromArray(): void
    {
        $audience = [Role::ASSISTANT, Role::USER];
        $priority = 0.8;

        $original = new Annotation(
            audience: $audience,
            priority: $priority
        );

        $array = $original->toArray();
        $recreated = Annotation::fromArray($array);

        $this->assertEquals(
            array_map(fn ($role) => $role->value, $original->getAudience() ?? []),
            array_map(fn ($role) => $role->value, $recreated->getAudience() ?? [])
        );
        $this->assertEquals($original->getPriority(), $recreated->getPriority());
    }
}
