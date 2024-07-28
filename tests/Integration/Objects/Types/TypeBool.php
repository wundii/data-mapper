<?php

declare(strict_types=1);

namespace Integration\Objects\Types;

final class TypeBool
{
    public function __construct(
        public bool $active,
        public bool $inactive,
    ) {
    }
}
