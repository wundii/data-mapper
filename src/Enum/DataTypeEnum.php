<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Enum;

enum DataTypeEnum: string
{
    case ARRAY = 'array';
    case BOOLEAN = 'bool';
    case FLOAT = 'float';
    case INTEGER = 'int';
    case NULL = 'null';
    case OBJECT = 'object';
    case STRING = 'string';

    public static function fromString(?string $type): string|self
    {
        return match ($type) {
            null, 'null' => self::NULL,
            'array' => self::ARRAY,
            'bool', 'boolean' => self::BOOLEAN,
            'float' => self::FLOAT,
            'int', 'integer' => self::INTEGER,
            'object' => self::OBJECT,
            'string' => self::STRING,
            default => $type,
        };
    }
}
