<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Interface;

use Wundii\DataMapper\Dto\AttributeDto;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\DataTypeEnum;

interface ElementDtoInterface
{
    public function getName(): string;

    public function getterName(): string;

    public function getAccessibleEnum(): AccessibleEnum;

    public function getDataType(): string|DataTypeEnum;

    public function getTargetType(): ?string;

    public function isNullable(): bool;

    /**
     * @return AttributeDto[]
     */
    public function getAttributes(): array;

    public function getStringValue(): string;

    public function getValue(): mixed;
}
