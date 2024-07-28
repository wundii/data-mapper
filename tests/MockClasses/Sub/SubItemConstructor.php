<?php

declare(strict_types=1);

namespace MockClasses\Sub;

use MockClasses\RootInterface;

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
