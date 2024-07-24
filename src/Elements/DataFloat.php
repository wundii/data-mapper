<?php

declare(strict_types=1);

namespace DataMapper\Elements;

use DataMapper\Interface\ElementDataInterface;

final readonly class DataFloat implements ElementDataInterface
{
    public function __construct(
        private float|string $value,
        private ?string $destination = null,
    ) {
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function getValue(): float
    {
        return filter_var($this->value, FILTER_VALIDATE_FLOAT) ?: (float) $this->value;
    }
}
