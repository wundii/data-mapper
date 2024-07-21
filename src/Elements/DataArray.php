<?php

declare(strict_types=1);

namespace DataMapper\Elements;

use DataMapper\Interface\ArrayElementInterface;
use DataMapper\Interface\DataElementInterface;

final readonly class DataArray implements ArrayElementInterface
{
    /**
     * @param DataElementInterface[] $value
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
     * @return DataElementInterface[]
     */
    public function getValue(): array
    {
        return $this->value;
    }
}
