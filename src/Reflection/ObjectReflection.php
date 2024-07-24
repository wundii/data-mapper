<?php

declare(strict_types=1);

namespace DataMapper\Reflection;

use DataMapper\Enum\ApproachEnum;

final readonly class ObjectReflection
{
    /**
     * @param PropertyReflection[] $properties
     * @param PropertyReflection[] $constructor
     * @param PropertyReflection[] $setters
     */
    public function __construct(
        private array $properties,
        private array $constructor,
        private array $setters,
    ) {
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
    public function getConstructor(): array
    {
        return $this->constructor;
    }

    /**
     * @return PropertyReflection[]
     */
    public function getSetters(): array
    {
        return $this->setters;
    }

    public function find(ApproachEnum $approachEnum, string $name): ?PropertyReflection
    {
        $properties = match ($approachEnum) {
            ApproachEnum::CONSTRUCTOR => $this->constructor,
            ApproachEnum::PROPERTY => $this->properties,
            ApproachEnum::SETTER => $this->setters,
        };

        foreach ($properties as $property) {
            if (strcasecmp($property->getName(), $name) === 0) {
                return $property;
            }
        }

        return null;
    }
}
