<?php

declare(strict_types=1);

namespace Wundii\DataMapper\SourceData;

use ReflectionException;
use Wundii\DataMapper\Dto\ReflectionObjectDto;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\Interface\SourceDataInterface;
use Wundii\DataMapper\Resolver\ReflectionClassResolver;

/**
 * @template T of object
 * @implements SourceDataInterface<T>
 */
abstract class AbstractSourceData implements SourceDataInterface
{
    /**
     * @var ReflectionObjectDto[]
     */
    protected static array $objectPropertyDtos = [];

    /**
     * @param string|array<int|string, mixed>|object $source
     * @param class-string<T>|T $objectOrClass
     * @param string[] $rootElementTree
     */
    public function __construct(
        protected DataConfigInterface $dataConfig,
        protected string|array|object $source,
        protected string|object $objectOrClass,
        protected array $rootElementTree = [],
        protected bool $forceInstance = false,
    ) {
    }

    /**
     * @param class-string<T>|T $objectOrClass
     * @throws DataMapperException|ReflectionException
     */
    public function resolveObjectDto(object|string $objectOrClass): ReflectionObjectDto
    {
        if (is_string($objectOrClass) && array_key_exists($objectOrClass, self::$objectPropertyDtos)) {
            return self::$objectPropertyDtos[$objectOrClass];
        }

        $reflectionObjectDto = (new ReflectionClassResolver())->resolve($objectOrClass);

        if (is_object($objectOrClass)) {
            return $reflectionObjectDto;
        }

        self::$objectPropertyDtos[$objectOrClass] = $reflectionObjectDto;

        return $reflectionObjectDto;
    }
}
