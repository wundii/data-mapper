<?php

declare(strict_types=1);

namespace DataMapper\Reflection;

final readonly class ObjectReflection
{
    /**
     * @param PropertyReflection[] $constructor
     * @param PropertyReflection[] $properties
     * @param PropertyReflection[] $setters
     */
    public function __construct(
        private array $constructor,
        private array $properties,
        private array $setters,
    ) {
    }

    /**
     * @return PropertyReflection[]
     */
    public function getConstructor(): array
    {
        return $this->constructor;
    }

    /**
     * @return PropertyReflection[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return PropertyReflection[]
     */
    public function getSetters(): array
    {
        return $this->setters;
    }
}
