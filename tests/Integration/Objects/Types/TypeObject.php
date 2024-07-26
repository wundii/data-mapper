<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\Types;

final class TypeObject
{
    public function __construct(
        public TypeString $typeString,
    ) {
    }
}
