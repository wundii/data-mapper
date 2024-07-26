<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\Types;

final class TypeInt
{
    public function __construct(
        public int $int,
    ) {
    }
}
