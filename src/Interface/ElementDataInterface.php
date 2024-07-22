<?php

declare(strict_types=1);

namespace DataMapper\Interface;

interface ElementDataInterface
{
    public function getDestination(): ?string;

    public function getValue(): mixed;
}
