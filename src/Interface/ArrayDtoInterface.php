<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Interface;

interface ArrayDtoInterface extends TypeDtoInterface
{
    /**
     * @return TypeDtoInterface[]
     */
    public function getValue(): array;
}
