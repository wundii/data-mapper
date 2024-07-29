<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Exception;

use Exception;
use Throwable;

class DataMapperException extends Exception
{
    public static function Error(string $message, int $code = 0, ?Throwable $throwable = null): self
    {
        return new self($message, $code, $throwable);
    }

    /**
     * @param (int|float|string)[] $arguments
     */
    public static function InvalidArgument(string $sourceData, null|array|string $arguments = null): DataMapperInvalidArgumentException
    {
        if (is_array($arguments)) {
            $arguments = implode(', ', array_map(
                static fn ($key, $value): string => is_string($key) ? sprintf('%s: %s', $key, $value) : (string) $value,
                array_keys($arguments),
                $arguments
            ));
        }

        if ($arguments !== null) {
            $sourceData .= ' - arguments: ' . $arguments;
        }

        return new DataMapperInvalidArgumentException($sourceData);
    }
}
