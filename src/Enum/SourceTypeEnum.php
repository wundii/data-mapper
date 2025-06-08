<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Enum;

enum SourceTypeEnum: string
{
    case JSON = 'json';
    case XML = 'xml';
}
