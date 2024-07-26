<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\Types;

use DataMapper\Tests\MockClasses\RootInterface;

final class TypeFloat implements RootInterface
{
    public function __construct(
        public float $float,
    ) {
    }
}
