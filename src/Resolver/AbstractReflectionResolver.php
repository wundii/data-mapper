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

/**
 * @template T of object
 */
abstract class AbstractReflectionResolver
{
    protected ReflectionElementResolver $reflectionElementResolver;

    /**
     * @var array<string, ReflectionClass<T>>
     */
    private static array $reflectionClassCache = [];

    /**
     * @var array<string, string[]>
     */
    private static array $reflectionClassPropertiesCache = [];

    /**
     * @var array<string, ElementDto>
     */
    private static array $reflectionClassElementCache = [];

    public function __construct(
    ) {
        $this->reflectionElementResolver = new ReflectionElementResolver();
    }

    /**
     * @param class-string<T>|T $objectOrClass
     * @return ReflectionClass<T>
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
     * @param class-string<T>|T $objectOrClass
     * @return string[]
     */
    public function reflectionClassPropertiesCache(object|string $objectOrClass): array
    {
        $classString = is_object($objectOrClass) ? get_class($objectOrClass) : $objectOrClass;

        if (array_key_exists($classString, self::$reflectionClassPropertiesCache)) {
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
     * @param class-string<T>|T $objectOrClass
     * @throws ReflectionException
     */
    public function reflectionElementsCache(
        object|string $objectOrClass,
        ReflectionProperty|ReflectionMethod $reflectionMethod,
        ReflectionProperty|ReflectionParameter $reflectionParameter,
        ?AnnotationDto $annotationDto
    ): ElementDto {
        $classString = is_object($objectOrClass) ? get_class($objectOrClass) : $objectOrClass;

        $key = $classString . '::' . $reflectionMethod->getName() . '::' . $reflectionParameter->getName();

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
