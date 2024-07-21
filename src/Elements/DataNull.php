<?php

declare(strict_types=1);

namespace DataMapper\Elements;

use DataMapper\Interface\DataElementInterface;

final readonly class DataNull implements DataElementInterface
{
    public function __construct(
        private null $value,
        private ?string $destination = null,
    ) {
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function getValue(): null
    {
        return $this->value;
    }
}
