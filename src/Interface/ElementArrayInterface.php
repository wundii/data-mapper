<?php

declare(strict_types=1);

namespace DataMapper\Interface;

interface ElementArrayInterface extends ElementDataInterface
{
    /**
     * @return ElementDataInterface[]
     */
    public function getValue(): array;
}
