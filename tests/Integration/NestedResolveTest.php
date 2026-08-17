<?php

declare(strict_types=1);

namespace Integration;

use MockClasses\NestedNode;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;

/**
 * The property and setter approach used to resolve the whole object tree twice
 * per nesting level, which grew exponentially (2^(depth+1)-1 instances).
 */
class NestedResolveTest extends TestCase
{
    /**
     * @return array<int|string, mixed>
     */
    public function nestedSource(int $depth): array
    {
        $node = [
            'name' => 'leaf',
        ];

        for ($i = 0; $i < $depth; $i++) {
            $node = [
                'name' => 'node' . $i,
                'child' => $node,
            ];
        }

        return $node;
    }

    public function testPropertyApproachResolvesEachNodeOnce(): void
    {
        NestedNode::$instances = 0;

        $dataMapper = new DataMapper(new DataConfig(ApproachEnum::PROPERTY));
        $node = $dataMapper->array($this->nestedSource(10), NestedNode::class);

        /** a chain of 10 children plus the root */
        $this->assertSame(11, NestedNode::$instances);
        $this->assertInstanceOf(NestedNode::class, $node);
        $this->assertSame('node9', $node->getName());
    }

    public function testSetterApproachResolvesEachNodeOnce(): void
    {
        NestedNode::$instances = 0;

        $dataMapper = new DataMapper(new DataConfig(ApproachEnum::SETTER));
        $node = $dataMapper->array($this->nestedSource(10), NestedNode::class);

        $this->assertSame(11, NestedNode::$instances);
        $this->assertInstanceOf(NestedNode::class, $node);
        $this->assertSame('node9', $node->getName());
    }

    public function testNestedValuesAreMappedCorrectly(): void
    {
        NestedNode::$instances = 0;

        $dataMapper = new DataMapper(new DataConfig(ApproachEnum::PROPERTY));
        $node = $dataMapper->array($this->nestedSource(3), NestedNode::class);

        $this->assertInstanceOf(NestedNode::class, $node);

        $names = [];
        $current = $node;
        while ($current instanceof NestedNode) {
            $names[] = $current->getName();
            $current = $current->getChild();
        }

        $this->assertSame(['node2', 'node1', 'node0', 'leaf'], $names);
    }
}
