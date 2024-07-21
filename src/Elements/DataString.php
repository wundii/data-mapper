<?php

declare(strict_types=1);

namespace DataMapper\Elements;

use DataMapper\Interface\DataElementInterface;

final readonly class DataString implements DataElementInterface
{
    public function __construct(
        private string $value,
        private ?string $destination = null,
    ) {
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
