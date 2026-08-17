<?php

declare(strict_types=1);

namespace MockClasses;

/**
 * Counts how often it is instantiated, to guard the number of object
 * constructions while mapping a nested structure.
 */
class NestedNode
{
    public static int $instances = 0;

    public string $name = '';

    public ?NestedNode $child = null;

    public function __construct()
    {
        ++self::$instances;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setChild(?self $child): void
    {
        $this->child = $child;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getChild(): ?self
    {
        return $this->child;
    }
}
