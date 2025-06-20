<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Enum;

enum ClassElementTypeEnum: string
{
    case ATTRIBUTE_TARGET = 'attribute_target';
    case ATTRIBUTE_SOURCE = 'attribute source';
    case CONSTRUCTOR = 'constructor';
    case GETTER = 'getter';
    case PROPERTY = 'property';
    case SETTER = 'setter';
}
