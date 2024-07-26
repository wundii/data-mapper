<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\Types;

use DataMapper\Tests\MockClasses\RootInterface;

final class TypeBool implements RootInterface
{
    public function __construct(
        public bool $active,
        public bool $inactive,
    ) {
    }
}
