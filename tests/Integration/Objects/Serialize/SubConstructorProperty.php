<?php

declare(strict_types=1);

namespace Integration\Objects\Serialize;

final class SubConstructorProperty
{
    public function __construct(
        public bool $active
    ) {
    }
}
