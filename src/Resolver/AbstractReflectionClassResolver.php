<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use ReflectionClass;
use Wundii\DataMapper\Exception\DataMapperException;

abstract class AbstractReflectionClassResolver
{
    /**
     * @var array<string, ReflectionClass>
     */
    private static array $reflectionClassCache = [];

    /**
     * @param object|class-string $objectOrClass
     */
    protected function getReflectionClass(string|object $objectOrClass): ReflectionClass
    {
        if (is_object($objectOrClass)) {
            $objectOrClass = get_class($objectOrClass);
        }

        if (isset(self::$reflectionClassCache[$objectOrClass])) {
            return self::$reflectionClassCache[$objectOrClass];
        }

        if (! class_exists($objectOrClass)) {
            throw DataMapperException::InvalidArgument(sprintf('The class "%s" does not exist.', $objectOrClass));
        }

        $reflectionClass = new ReflectionClass($objectOrClass);
        self::$reflectionClassCache[$objectOrClass] = $reflectionClass;

        return $reflectionClass;
    }
}