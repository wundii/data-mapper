<?php

declare(strict_types=1);

namespace Integration\Objects\ApproachBasic;

final class SubConstructor
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
