<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Attribute;

use Attribute;
use Wundii\DataMapper\Interface\AttributeInterface;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class TestData implements AttributeInterface
{
    public function __construct(
        private string $description
    ) {
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getName(): string
    {
        return $this->getDescription();
    }

    public function getValue(): ?string
    {
        return null;
    }
}
