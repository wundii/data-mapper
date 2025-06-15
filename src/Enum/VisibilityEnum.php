<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Enum;

enum VisibilityEnum: string
{
    case PRIVATE = 'Private';
    case PROTECTED = 'Protected';
    case PUBLIC = 'Public';
}
