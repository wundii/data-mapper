<?php

declare(strict_types=1);

namespace DataMapper\Interface;

interface ElementObjectInterface extends ElementDataInterface
{
    public function getObjectName(): string;

    /**
     * @return ElementDataInterface[]
     */
    public function getValue(): array;
}
