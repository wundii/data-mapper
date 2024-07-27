<?php

declare(strict_types=1);

namespace DataMapper\SourceData;

use DataMapper\DataConfig;
use DataMapper\Interface\SourceDataInterface;
use DataMapper\Reflection\ObjectReflection;
use DataMapper\Resolver\ReflectionObjectResolver;
use Exception;

abstract class AbstractSourceData implements SourceDataInterface
{
    /**
     * @var ObjectReflection[]
     */
    protected static array $objectReflections = [];

    public function __construct(
        protected DataConfig $dataConfig,
        protected string $source,
        protected string|object $object,
    ) {
    }

    /**
     * @throws Exception
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
