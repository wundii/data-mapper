<?php

declare(strict_types=1);

namespace MockClasses;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class ObjectAttribute
{
}
