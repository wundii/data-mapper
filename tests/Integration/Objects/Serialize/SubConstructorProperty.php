<?php

declare(strict_types=1);

namespace Integration\Objects\Serialize;

final class SubConstructorProperty
{
    public function __construct(
        private bool $active
    ) {
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
