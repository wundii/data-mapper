<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\Types;

final class TypeString
{
    public function __construct(
        public string $string,
    ) {
    }
}
