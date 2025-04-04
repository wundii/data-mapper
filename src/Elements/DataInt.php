<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Elements;

use Wundii\DataMapper\Interface\ElementValueInterface;

final readonly class DataInt implements ElementValueInterface
{
    public function __construct(
        private int|float|string $value,
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

    public function getValue(): int
    {
        return filter_var($this->value, FILTER_VALIDATE_INT) ?: (int) $this->value;
    }
}
