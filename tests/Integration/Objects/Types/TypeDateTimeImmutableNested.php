<?php

declare(strict_types=1);

namespace Integration\Objects\Types;

final class TypeDateTimeImmutableNested
{
    public function __construct(
        public \DateTimeImmutable $start,
        public \DateTimeImmutable $end,
        public float $value,
    ) {
    }
}
