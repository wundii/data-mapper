<?php

declare(strict_types=1);

namespace Integration\Objects\Serialize;

use InvalidArgumentException;

final class SubConstructorProperty
{
    public function __construct(
        private bool $active
    ) {
    }

    public function setActive(bool $active, string $comment): void
    {
        if ($comment !== 'set active') {
            throw new InvalidArgumentException('Invalid comment provided');
        }

        $this->active = $active;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
