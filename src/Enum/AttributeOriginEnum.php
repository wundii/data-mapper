<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Enum;

enum AttributeOriginEnum: string
{
    case TARGET_CLASS = 'class';
    case TARGET_FUNCTION = 'function';
    case TARGET_METHOD = 'method';
    case TARGET_PROPERTY = 'property';
}
