<?php

declare(strict_types=1);

namespace DataMapper\Enum;

enum DataTypeEnum
{
    case STRING;
    case INTEGER;
    case FLOAT;
    case BOOLEAN;
    case ARRAY;
    case OBJECT;
}
