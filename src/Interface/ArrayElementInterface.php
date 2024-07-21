<?php

declare(strict_types=1);

namespace DataMapper\Interface;

interface ArrayElementInterface extends DataElementInterface
{
    /**
     * @return DataElementInterface[]
     */
    public function getValue(): array;
}
