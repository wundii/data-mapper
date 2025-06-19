<?php

declare(strict_types=1);

namespace Wundii\DataMapper\SourceData;

use ReflectionException;
use Wundii\DataMapper\Dto\ObjectDto;
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
     * @var ObjectDto[]
     */
    protected static array $objectDtos = [];

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
    public function resolveObjectDto(string|object $object): ObjectDto
    {
        if (is_string($object) && array_key_exists($object, self::$objectDtos)) {
            return self::$objectDtos[$object];
        }

        $objectDto = (new ReflectionObjectResolver())->resolve($object);

        if (is_object($object)) {
            return $objectDto;
        }

        self::$objectDtos[$object] = $objectDto;

        return $objectDto;
    }
}
