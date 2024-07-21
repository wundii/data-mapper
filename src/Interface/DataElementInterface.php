<?php

declare(strict_types=1);

namespace DataMapper\Interface;

interface DataElementInterface
{
    public function getDestination(): ?string;

    public function getValue(): mixed;
}
