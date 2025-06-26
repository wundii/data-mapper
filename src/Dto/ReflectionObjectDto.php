<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

use Wundii\DataMapper\Attribute\SourceData;
use Wundii\DataMapper\Attribute\TargetData;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Interface\ElementDtoInterface;

final readonly class ReflectionObjectDto
{
    /**
     * @param AttributeDto[] $attributesClass
     * @param PropertyDto[] $propertiesClass
     * @param PropertyDto[] $propertiesConst
     * @param MethodDto[] $methodGetters
     * @param MethodDto[] $methodOthers
     * @param MethodDto[] $methodSetters
     */
    public function __construct(
        private array $attributesClass,
        private array $propertiesClass,
        private array $propertiesConst,
        private array $methodGetters,
        private array $methodOthers,
        private array $methodSetters,
    ) {
    }

    /**
     * @return AttributeDto[]
     */
    public function getAttributesClass(): array
    {
        return $this->attributesClass;
    }

    /**
     * @return PropertyDto[]
     */
    public function getPropertiesClass(): array
    {
        return $this->propertiesClass;
    }

    /**
     * @return PropertyDto[]
     */
    public function getPropertiesConst(): array
    {
        return $this->propertiesConst;
    }

    /**
     * @return MethodDto[]
     */
    public function getMethodGetters(): array
    {
        return $this->methodGetters;
    }

    /**
     * @return MethodDto[]
     */
    public function getMethodOther(): array
    {
        return $this->methodOthers;
    }

    /**
     * @return MethodDto[]
     */
    public function getMethodSetters(): array
    {
        return $this->methodSetters;
    }

    /**
     * @return AttributeDto[]
     */
    public function getAttributes(): array
    {
        return $this->attributesClass;
    }

    /**
     * @return ElementDtoInterface[]
     */
    public function availableData(): array
    {
        $data = [];
        foreach ($this->propertiesClass as $propertyClass) {
            if ($propertyClass->getAccessibleEnum() !== AccessibleEnum::PUBLIC) {
                continue;
            }

            $data[$propertyClass->getName()] = $propertyClass;
        }

        foreach ($this->methodGetters as $methodGetter) {
            if ($methodGetter->getAccessibleEnum() !== AccessibleEnum::PUBLIC) {
                continue;
            }

            $data[$methodGetter->getName()] = $methodGetter;

            foreach ($methodGetter->getAttributes() as $attribute) {
                if ($attribute->getClassString() !== SourceData::class) {
                    continue;
                }

                if (! is_string($attribute->getArguments()['target'])) {
                    continue;
                }

                $data[$attribute->getArguments()['target']] = $methodGetter;
            }
        }

        return $data;
    }

    public function findElementDto(ApproachEnum $approachEnum, string $name): ?ElementDtoInterface
    {
        $elementDto = match ($approachEnum) {
            ApproachEnum::CONSTRUCTOR => $this->propertiesConst,
            ApproachEnum::PROPERTY => $this->propertiesClass,
            ApproachEnum::SETTER => $this->methodSetters,
        };

        foreach ($elementDto as $propertyDto) {
            $propertyName = $propertyDto->getName();

            foreach ($propertyDto->getAttributes() as $attribute) {
                if (
                    $attribute->getClassString() === TargetData::class
                    && $attribute->getArguments()['alias'] === $name
                ) {
                    return $propertyDto;
                }
            }

            if (
                $approachEnum === ApproachEnum::SETTER
                && strcasecmp(substr($propertyName, 3), $name) === 0
            ) {
                return $propertyDto;
            }

            if (strcasecmp($propertyName, $name) === 0) {
                return $propertyDto;
            }
        }

        return null;
    }
}
