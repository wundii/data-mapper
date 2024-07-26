<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\Types;

final class TypeNull
{
    public function __construct(
        public ?string $string,
    ) {
    }
}
