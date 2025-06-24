<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

use Wundii\DataMapper\Attribute\SourceData;
use Wundii\DataMapper\Attribute\TargetData;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ApproachEnum;

final readonly class ReflectionObjectDto
{
    private array $test;

    /**
     * @param PropertyDto[] $attributes
     * @param PropertyDto[] $propertiesCLass
     * @param PropertyDto[] $propertiesConst
     * @param PropertyDto[] $methodGetters
     * @param PropertyDto[] $methodSetters
     */
    public function __construct(
        private array $attributes,
        private array $propertiesCLass,
        private array $propertiesConst,
        private array $methodGetters,
        private array $methodOthers,
        private array $methodSetters,
    ) {
        $this->test = [];
    }

    /**
     * @return PropertyDto[]
     */
    public function getPropertiesCLass(): array
    {
        return $this->propertiesCLass;
    }

    /**
     * @return PropertyDto[]
     */
    public function getPropertiesConst(): array
    {
        return $this->propertiesConst;
    }

    /**
     * @return PropertyDto[]
     */
    public function getMethodGetters(): array
    {
        return $this->methodGetters;
    }

    /**
     * @return PropertyDto[]
     */
    public function getMethodSetters(): array
    {
        return $this->methodSetters;
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
        foreach ($this->propertiesCLass as $property) {
            if ($property->getAccessibleEnum() !== AccessibleEnum::PUBLIC) {
                continue;
            }

            $data[$property->getName()] = $property;
        }

        foreach ($this->methodGetters as $getter) {
            if ($getter->getAccessibleEnum() !== AccessibleEnum::PUBLIC) {
                continue;
            }

            $data[$getter->getName()] = $getter;
        }

        foreach ($this->attributes as $attribute) {
            if ($attribute->getAccessibleEnum() !== AccessibleEnum::PUBLIC) {
                continue;
            }

            if ($attribute->getAttributeClassString() !== SourceData::class) {
                continue;
            }

            $data[$attribute->getName()] = $attribute;
        }

        return $data;
    }

    public function findAttributeTargetPropertyDto(string $name): ?PropertyDto
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getAttributeClassString() !== TargetData::class) {
                continue;
            }

            if (strcasecmp($attribute->getName(), $name) === 0) {
                return $attribute;
            }
        }

        return null;
    }

    public function findPropertyDto(ApproachEnum $approachEnum, string $name): ?PropertyDto
    {
        $propertyDtos = match ($approachEnum) {
            ApproachEnum::CONSTRUCTOR => $this->propertiesConst,
            ApproachEnum::PROPERTY => $this->propertiesCLass,
            ApproachEnum::SETTER => $this->methodSetters,
        };

        $propertyDto = $this->findAttributeTargetPropertyDto($name);
        if ($propertyDto instanceof PropertyDto) {
            return $propertyDto;
        }

        foreach ($propertyDtos as $propertyDto) {
            $propertyName = $propertyDto->getName();
            if (
                $approachEnum === ApproachEnum::SETTER
                && str_starts_with($propertyName, 'set')
            ) {
                $propertyName = substr($propertyName, 3);
            }

            if (strcasecmp($propertyName, $name) === 0) {
                return $propertyDto;
            }
        }

        return null;
    }
}
