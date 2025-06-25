<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use Wundii\DataMapper\Dto\AnnotationDto;
use Wundii\DataMapper\Dto\ElementDto;
use Wundii\DataMapper\Exception\DataMapperException;

abstract class AbstractReflectionClassResolver
{
    /**
     * @var array<string, ReflectionClass>
     */
    private static array $reflectionClassCache = [];

    private static array $reflectionClassPropertiesCache = [];

    private static array $reflectionClassElementCache = [];

    protected ReflectionElementResolver $reflectionElementResolver;

    public function __construct(
    ) {
        $this->reflectionElementResolver = new ReflectionElementResolver();
    }

    /**
     * @param object|class-string $objectOrClass
     */
    public function reflectionClassCache(object|string $objectOrClass): ReflectionClass
    {
        $classString = is_object($objectOrClass) ? get_class($objectOrClass) : $objectOrClass;

        if (isset(self::$reflectionClassCache[$classString])) {
            return self::$reflectionClassCache[$classString];
        }

        if (! class_exists($classString)) {
            throw DataMapperException::InvalidArgument(sprintf('The class "%s" does not exist.', $classString));
        }

        $reflectionClass = new ReflectionClass($classString);
        self::$reflectionClassCache[$classString] = $reflectionClass;

        return $reflectionClass;
    }

    /**
     * @param object|class-string $objectOrClass
     * @return string[]
     */
    public function reflectionClassPropertiesCache(object|string $objectOrClass): array
    {
        $classString = is_object($objectOrClass) ? get_class($objectOrClass) : $objectOrClass;

        if (isset(self::$reflectionClassPropertiesCache[$classString])) {
            return self::$reflectionClassPropertiesCache[$classString];
        }

        $reflectionClass = $this->reflectionClassCache($classString);
        $properties = $reflectionClass->getProperties();
        $reflectionClassProperties = [];

        foreach ($properties as $property) {
            $reflectionClassProperties[] = $property->getName();
        }
        self::$reflectionClassPropertiesCache[$classString] = $reflectionClassProperties;

        return $reflectionClassProperties;
    }

    /**
     * @param object|class-string $objectOrClass
     * @throws ReflectionException
     */
    public function reflectionElementsCache(
        object|string $objectOrClass,
        ReflectionProperty|ReflectionMethod $reflectionMethod,
        ReflectionProperty|ReflectionParameter $reflectionParameter,
        ?AnnotationDto $annotationDto
    ): ElementDto
    {
        $classString = is_object($objectOrClass) ? get_class($objectOrClass) : $objectOrClass;

        $key = $classString . '::' . $reflectionParameter->getName();

        if (isset(self::$reflectionClassElementCache[$key])) {
            return self::$reflectionClassElementCache[$key];
        }

        $elementDto = $this->reflectionElementResolver->resolve(
            $objectOrClass,
            $reflectionMethod,
            $reflectionParameter,
            $annotationDto
        );
        self::$reflectionClassElementCache[$key] = $elementDto;

        return $elementDto;
    }
}