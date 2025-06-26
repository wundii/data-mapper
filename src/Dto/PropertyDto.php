<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

use Wundii\DataMapper\Attribute\SourceData;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\DataTypeEnum;
use Wundii\DataMapper\Interface\ElementDtoInterface;

final readonly class PropertyDto implements ElementDtoInterface
{
    /**
     * @param AttributeDto[] $attributes
     */
    public function __construct(
        private AccessibleEnum $accessibleEnum,
        private string $name,
        private string|DataTypeEnum $dataType,
        private ?string $targetType,
        private bool $nullable,
        private bool $isDefaultValueAvailable = false,
        private mixed $defaultValue = null,
        private mixed $value = null,
        private ?AnnotationDto $annotationDto = null,
        private array $attributes = [],
    ) {
    }

    public function getDataType(): string|DataTypeEnum
    {
        return $this->dataType;
    }

    public function getTargetType(): ?string
    {
        return $this->targetType;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getterName(): string
    {
        $sourceName = $this->name;

        foreach ($this->attributes as $attribute) {
            if ($attribute->getClassString() !== SourceData::class) {
                continue;
            }

            if (! is_string($attribute->getArguments()['target'])) {
                continue;
            }

            $sourceName = $attribute->getArguments()['target'];
        }

        return $sourceName;
    }

    public function getAccessibleEnum(): AccessibleEnum
    {
        return $this->accessibleEnum;
    }

    public function isDefaultValueAvailable(): bool
    {
        return $this->isDefaultValueAvailable;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getStringValue(): string
    {
        if (
            $this->value === null
            || is_int($this->value)
            || is_float($this->value)
            || is_numeric($this->value)
            || is_bool($this->value)
            || is_string($this->value)
        ) {
            return (string) $this->value;
        }

        return '';
    }

    public function getAnnotationDto(): ?AnnotationDto
    {
        return $this->annotationDto;
    }

    /**
     * @return AttributeDto[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
