<?php

declare(strict_types=1);

namespace DataMapper\Elements;

use DataMapper\Interface\ElementArrayInterface;
use DataMapper\Interface\ElementDataInterface;

final readonly class DataArray implements ElementArrayInterface
{
    /**
     * @param ElementDataInterface[] $value
     */
    public function __construct(
        private array $value,
        private ?string $destination = null,
    ) {
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    /**
     * @return ElementDataInterface[]
     */
    public function getValue(): array
    {
        return $this->value;
    }
}
