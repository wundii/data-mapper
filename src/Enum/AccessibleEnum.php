<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Enum;

enum AccessibleEnum: string
{
    case PUBLIC = 'Public';
    case PROTECTED = 'Protected';
    case PRIVATE = 'Private';
}
