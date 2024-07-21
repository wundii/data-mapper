<?php

declare(strict_types=1);

namespace DataMapper\Tests\MockClasses;

final readonly class ItemClassConstructor implements RootClassInterface
{
    public function __construct(
        private float $price,
        private bool $isAvailable,
    ) {
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}
