<?php

declare(strict_types=1);

namespace DataMapper\Tests\MockClasses\Sub;

use DataMapper\Tests\MockClasses\RootInterface;

final readonly class SubItemConstructor implements RootInterface
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
