<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Enum;

enum SourceTypeEnum: string
{
    case ARRAY = 'Array';
    case JSON = 'Json';
    case OBJECT = 'Object';
    case XML = 'XML';
}
