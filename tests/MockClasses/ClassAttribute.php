<?php

declare(strict_types=1);

namespace MockClasses;

use Attribute;
use Wundii\DataMapper\Interface\AttributeInterface;

#[Attribute(Attribute::TARGET_CLASS)]
class ClassAttribute implements AttributeInterface
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
