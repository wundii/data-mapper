<?php

declare(strict_types=1);

namespace Integration\Objects\UnknownAttribute;

use Vendor\NotInstalled\Attribute\Description;
use Vendor\NotInstalled\Attribute\Documentation;

#[Documentation('class attribute of a not installed package')]
final class UnknownAttributeConstructor
{
    public function __construct(
        #[Description('constructor parameter attribute of a not installed package')]
        public string $name,
        public int $count = 0,
    ) {
    }
}
