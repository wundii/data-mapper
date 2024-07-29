<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Elements;

use Wundii\DataMapper\Interface\ElementDataInterface;

final readonly class DataString implements ElementDataInterface
{
    public function __construct(
        private string $value,
        private ?string $destination = null,
    ) {
    }

    public function __toString(): string
    {
        return $this->value;
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
