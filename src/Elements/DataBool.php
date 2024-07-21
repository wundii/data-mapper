<?php

declare(strict_types=1);

namespace DataMapper\Elements;

use DataMapper\Interface\DataElementInterface;

final readonly class DataBool implements DataElementInterface
{
    public function __construct(
        private bool $value,
        private ?string $destination = null,
    ) {
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function getValue(): bool
    {
        return $this->value;
    }
}
