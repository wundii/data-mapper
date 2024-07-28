<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Reflection;

use Wundii\DataMapper\Enum\ApproachEnum;

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
            $propertyName = $property->getName();
            if (
                $approachEnum === ApproachEnum::SETTER
                && str_starts_with($propertyName, 'set')
            ) {
                $propertyName = substr($propertyName, 3);
            }

            if (strcasecmp($propertyName, $name) === 0) {
                return $property;
            }
        }

        return null;
    }
}
