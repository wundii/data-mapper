<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\Types;

use DataMapper\Tests\MockClasses\RootInterface;

final class TypeObject implements RootInterface
{
    public function __construct(
        public TypeString $typeString,
    ) {
    }
}
