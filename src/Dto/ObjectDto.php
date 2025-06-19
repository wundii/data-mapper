<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ApproachEnum;

final readonly class ObjectDto
{
    /**
     * @param PropertyDto[] $properties
     * @param PropertyDto[] $constructor
     * @param PropertyDto[] $getters
     * @param PropertyDto[] $setters
     * @param PropertyDto[] $attributes
     */
    public function __construct(
        private array $properties,
        private array $constructor,
        private array $getters,
        private array $setters,
        private array $attributes,
    ) {
    }

    /**
     * @return PropertyDto[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return PropertyDto[]
     */
    public function getConstructor(): array
    {
        return $this->constructor;
    }

    /**
     * @return PropertyDto[]
     */
    public function getGetters(): array
    {
        return $this->getters;
    }

    /**
     * @return PropertyDto[]
     */
    public function getSetters(): array
    {
        return $this->setters;
    }

    /**
     * @return PropertyDto[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return PropertyDto[]
     */
    public function availableData(): array
    {
        $data = [];
        foreach ($this->properties as $property) {
            if ($property->getAccessibleEnum() !== AccessibleEnum::PUBLIC) {
                continue;
            }

            $data[$property->getName()] = $property;
        }

        foreach ($this->getters as $getter) {
            if ($getter->getAccessibleEnum() !== AccessibleEnum::PUBLIC) {
                continue;
            }

            $data[$getter->getName()] = $getter;
        }

        foreach ($this->attributes as $attribute) {
            if ($attribute->getAccessibleEnum() !== AccessibleEnum::PUBLIC) {
                continue;
            }

            $data[$attribute->getName()] = $attribute;
        }

        return $data;

    }

    public function findPropertyDto(ApproachEnum $approachEnum, string $name): ?PropertyDto
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
