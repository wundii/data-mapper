<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\DataTypeEnum;

final readonly class ElementDto
{
    public function __construct(
        private string $name,
        private string|DataTypeEnum $dataType,
        private ?string $targetType = null,
        private bool $nullable = false,
        private AccessibleEnum $accessibleEnum = AccessibleEnum::PUBLIC,
        private bool $isDefaultValueAvailable = false,
        private mixed $defaultValue = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
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
}