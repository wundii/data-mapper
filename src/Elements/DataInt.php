<?php

declare(strict_types=1);

namespace DataMapper\Elements;

use DataMapper\Interface\ElementDataInterface;

final readonly class DataInt implements ElementDataInterface
{
    public function __construct(
        private int $value,
        private ?string $destination = null,
    ) {
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}