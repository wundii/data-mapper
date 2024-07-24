<?php

declare(strict_types=1);

namespace DataMapper\Tests\MockClasses\Sub;

use DataMapper\Tests\MockClasses\RootInterface;

final readonly class SubItemConstructor implements RootInterface
{
    public function __construct(
        private string $product,
    ) {
    }

    public function getProduct(): string
    {
        return $this->product;
    }
}
