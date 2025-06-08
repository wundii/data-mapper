<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Enum;

enum ApproachEnum: string
{
    case CONSTRUCTOR = 'constructor';
    case PROPERTY = 'property';
    case SETTER = 'setter';
}
