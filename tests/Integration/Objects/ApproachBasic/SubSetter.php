<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\ApproachBasic;

final class SubSetter
{
    private bool $active;

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
