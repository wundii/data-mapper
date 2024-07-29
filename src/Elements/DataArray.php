<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Elements;

use Wundii\DataMapper\Interface\ElementArrayInterface;
use Wundii\DataMapper\Interface\ElementDataInterface;

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

    public function __toString(): string
    {
        $value = array_slice($this->value, 0, 3);
        return implode(', ', $value);
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
