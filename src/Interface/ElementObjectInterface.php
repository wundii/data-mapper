<?php

declare(strict_types=1);

namespace DataMapper\Interface;

interface ElementObjectInterface extends ElementDataInterface
{
    public function getObject(): string|object;

    /**
     * @return ElementDataInterface[]
     */
    public function getValue(): array;

    public function directValue(): bool;
}
