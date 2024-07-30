<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Reflection;

use Wundii\DataMapper\Enum\DataTypeEnum;

final readonly class PropertyReflection
{
    public function __construct(
        private string $name,
        private string|DataTypeEnum $dataType,
        private ?string $targetType,
        private bool $oneType,
        private bool $nullable,
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
}
