<?php

declare(strict_types=1);

namespace Integration\Objects\UnknownAttribute;

use Vendor\NotInstalled\Attribute\Description;
use Vendor\NotInstalled\Attribute\Documentation;

#[Documentation('class attribute of a not installed package')]
final class UnknownAttributeSetter
{
    #[Description('property attribute of a not installed package')]
    private ?string $name = null;

    private int $count = 0;

    public function getName(): ?string
    {
        return $this->name;
    }

    #[Description('method attribute of a not installed package')]
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }
}
