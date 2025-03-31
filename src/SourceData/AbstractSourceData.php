<?php

declare(strict_types=1);

namespace Wundii\DataMapper\SourceData;

use ReflectionException;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\Interface\SourceDataInterface;
use Wundii\DataMapper\Reflection\ObjectReflection;
use Wundii\DataMapper\Resolver\ReflectionObjectResolver;

/**
 * @template T of object
 * @implements SourceDataInterface<T>
 */
abstract class AbstractSourceData implements SourceDataInterface
{
    /**
     * @var ObjectReflection[]
     */
    protected static array $objectReflections = [];

    /**
     * @param class-string<T>|T $object
     * @param string[] $rootElementTree
     */
    public function __construct(
        protected DataConfigInterface $dataConfig,
        protected string $source,
        protected string|object $object,
        protected array $rootElementTree = [],
        protected bool $forceInstance = false,
    ) {
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    public function reflectionObject(string|object $object): ObjectReflection
    {
        if (is_string($object) && array_key_exists($object, self::$objectReflections)) {
            return self::$objectReflections[$object];
        }

        $objectReflection = (new ReflectionObjectResolver())->resolve($object);

        if (is_object($object)) {
            return $objectReflection;
        }

        self::$objectReflections[$object] = $objectReflection;

        return $objectReflection;
    }
}
