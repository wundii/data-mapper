<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Elements;

use Wundii\DataMapper\Interface\ElementValueInterface;

final readonly class DataFloat implements ElementValueInterface
{
    public function __construct(
        private float|string $value,
        private ?string $destination = null,
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->value;
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
