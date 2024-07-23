<?php

declare(strict_types=1);

namespace DataMapper\Enum;

enum DataTypeEnum: string
{
    const STRING = 'string';
    const INTEGER = 'int';
    const FLOAT = 'float';
    const BOOLEAN = 'bool';
    const ARRAY = 'array';
    const OBJECT = 'object';
}
