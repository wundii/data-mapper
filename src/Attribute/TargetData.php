<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Attribute;

use Attribute;
use Wundii\DataMapper\Interface\AttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class TargetData implements AttributeInterface
{
    public function __construct(
        private string $alias
    ) {
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getName(): string
    {
        return $this->getAlias();
    }

    public function getValue(): ?string
    {
        return null;
    }
}
