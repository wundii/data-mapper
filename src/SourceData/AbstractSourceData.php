<?php

declare(strict_types=1);

namespace Wundii\DataMapper\SourceData;

use ReflectionException;
use Wundii\DataMapper\Dto\ObjectPropertyDto;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\Interface\SourceDataInterface;
use Wundii\DataMapper\Resolver\ReflectionObjectResolver;

/**
 * @template T of object
 * @implements SourceDataInterface<T>
 */
abstract class AbstractSourceData implements SourceDataInterface
{
    /**
     * @var ObjectPropertyDto[]
     */
    protected static array $objectPropertyDtos = [];

    /**
     * @param string|array<int|string, mixed>|object $source
     * @param class-string<T>|T $object
     * @param string[] $rootElementTree
     */
    public function __construct(
        protected DataConfigInterface $dataConfig,
        protected string|array|object $source,
        protected string|object $object,
        protected array $rootElementTree = [],
        protected bool $forceInstance = false,
    ) {
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    public function resolveObjectPropertyDto(string|object $object): ObjectPropertyDto
    {
        if (is_string($object) && array_key_exists($object, self::$objectPropertyDtos)) {
            return self::$objectPropertyDtos[$object];
        }

        $objectPropertyDto = (new ReflectionObjectResolver())->resolve($object);

        if (is_object($object)) {
            return $objectPropertyDto;
        }

        self::$objectPropertyDtos[$object] = $objectPropertyDto;

        return $objectPropertyDto;
    }
}
