<?php

declare(strict_types=1);

namespace DataMapper\Reflection;

final readonly class PropertyReflection
{
    /**
     * @param string[] $types
     */
    public function __construct(
        private string $target,
        private array $types,
        private ?string $classString,
    ) {
    }

    public function getClassString(): ?string
    {
        return $this->classString;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }
}
