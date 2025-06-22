<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Attribute;

use Attribute;
use Wundii\DataMapper\Interface\AttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class SourceData implements AttributeInterface
{
    public function __construct(
        private string $target
    ) {
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getName(): string
    {
        return $this->getTarget();
    }

    public function getValue(): ?string
    {
        return null;
    }
}
