<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Reflection;

use Wundii\DataMapper\Enum\DataTypeEnum;
use Wundii\DataMapper\Enum\VisibilityEnum;

final readonly class PropertyReflection
{
    public function __construct(
        private string $name,
        private string|DataTypeEnum $dataType,
        private ?string $targetType,
        private bool $oneType,
        private bool $nullable,
        private VisibilityEnum $visibilityEnum,
        private mixed $value = null,
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

    public function getVisibilityEnum(): VisibilityEnum
    {
        return $this->visibilityEnum;
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
     * @return PropertyReflection[]
     */
    public function getChildrenProperty(): array
    {
        return [];
    }
}
