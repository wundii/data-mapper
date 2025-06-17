<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Enum;

enum SourceTypeEnum: string
{
    case ARRAY = 'Array';
    case INI = 'Ini';
    case JSON = 'Json';
    case NEON = 'Neon';
    case OBJECT = 'Object';
    case XML = 'XML';
    case YAML = 'Yaml';
}
