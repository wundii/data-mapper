<?php

declare(strict_types=1);

namespace DataMapper\Interface;

interface ObjectElementInterface extends DataElementInterface
{
    public function getObjectName(): string;

    /**
     * @return DataElementInterface[]
     */
    public function getValue(): array;
}
