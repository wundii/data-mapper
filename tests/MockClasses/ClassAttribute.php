<?php

declare(strict_types=1);

namespace MockClasses;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ClassAttribute
{
    public function __construct(
        private ?string $name = null,
        private ?string $value = null,
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}
