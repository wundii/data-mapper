<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Interface;

interface ObjectDtoInterface extends TypeDtoInterface
{
    public function getObject(): string|object;

    /**
     * @return TypeDtoInterface[]
     */
    public function getValue(): array;

    public function directValue(): bool;
}
