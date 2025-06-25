<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Enum;

enum MethodTypeEnum: string
{
    case CONSTRUCTOR = 'constructor';
    case GETTER = 'getter';
    case SETTER = 'setter';
    case OTHER = 'other';
}
