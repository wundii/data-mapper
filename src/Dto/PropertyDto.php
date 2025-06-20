<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ClassElementTypeEnum;
use Wundii\DataMapper\Enum\DataTypeEnum;

final readonly class PropertyDto
{
    public function __construct(
        private ClassElementTypeEnum $classElementTypeEnum,
        private string $name,
        private string|DataTypeEnum $dataType,
        private ?string $targetType,
        private bool $oneType,
        private bool $nullable,
        private AccessibleEnum $accessibleEnum,
        private mixed $value = null,
    ) {
    }

    public function getClassElementTypeEnum(): ClassElementTypeEnum
    {
        return $this->classElementTypeEnum;
    }

    public function getDataType(): string|DataTypeEnum
    {
        return $this->dataType;
    }

    public function getTargetType(): ?string
    {
        return $this->targetType;
    }

    public function isOneType(): bool
    {
        return $this->oneType;
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
}
