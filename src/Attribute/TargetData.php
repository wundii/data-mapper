<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class TargetData
{
    public function __construct(
        private string $alias
    ) {
    }

    public function getAlias(): string
    {
        return $this->alias;
    }
}
