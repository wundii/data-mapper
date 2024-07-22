<?php

declare(strict_types=1);

namespace DataMapper\Elements;

use DataMapper\Interface\ElementDataInterface;

final readonly class DataFloat implements ElementDataInterface
{
    public function __construct(
        private float $value,
        private ?string $destination = null,
    ) {
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
