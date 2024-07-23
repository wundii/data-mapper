<?php

declare(strict_types=1);

namespace DataMapper\Enum;

enum DataTypeEnum: string
{
    public const STRING = 'string';

    public const INTEGER = 'int';

    public const FLOAT = 'float';

    public const BOOLEAN = 'bool';

    public const ARRAY = 'array';

    public const OBJECT = 'object';
}
