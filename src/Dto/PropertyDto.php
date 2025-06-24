<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\DataTypeEnum;

final readonly class PropertyDto
{
    /**
     * @param AnnotationDto[] $annotations
     * @param AttributeDto[] $attributes
     */
    public function __construct(
        private string $name,
        private string|DataTypeEnum $dataType,
        private ?string $targetType,
        private bool $nullable,
        private AccessibleEnum $accessibleEnum,
        private bool $isDefaultValueAvailable = false,
        private mixed $defaultValue = null,
        private mixed $value = null,
        private array $annotations = [],
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

    /**
     * @return AnnotationDto[]
     */
    public function getAnnotations(): array
    {
        return $this->annotations;
    }

    /**
     * @return AttributeDto[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
