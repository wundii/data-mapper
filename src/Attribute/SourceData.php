<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class SourceData
{
    public function __construct(
        private string $target
    ) {
    }

    public function getTarget(): string
    {
        return $this->target;
    }
}
