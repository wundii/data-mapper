<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Enum;

enum AccessibleEnum: string
{
    case PUBLIC = 'public';
    case PROTECTED = 'protected';
    case PRIVATE = 'private';
}
