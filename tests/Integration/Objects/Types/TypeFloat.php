<?php

declare(strict_types=1);

namespace Integration\Objects\Types;

final class TypeFloat
{
    public function __construct(
        public float $float,
    ) {
    }
}
