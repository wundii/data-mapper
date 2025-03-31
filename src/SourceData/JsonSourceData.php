<?php

declare(strict_types=1);

namespace Wundii\DataMapper\SourceData;

use ReflectionException;
use Wundii\DataMapper\Elements\DataArray;
use Wundii\DataMapper\Elements\DataBool;
use Wundii\DataMapper\Elements\DataFloat;
use Wundii\DataMapper\Elements\DataInt;
use Wundii\DataMapper\Elements\DataNull;
use Wundii\DataMapper\Elements\DataObject;
use Wundii\DataMapper\Elements\DataString;
use Wundii\DataMapper\Enum\DataTypeEnum;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\Interface\ElementArrayInterface;
use Wundii\DataMapper\Interface\ElementObjectInterface;
use Wundii\DataMapper\Reflection\PropertyReflection;
use Wundii\DataMapper\Resolver\ElementObjectResolver;

if (PHP_VERSION_ID < 80300) {
    function json_validate(string $string): bool
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}

/**
 * @template T of object
 * @extends AbstractSourceData<T>
 */
final class JsonSourceData extends AbstractSourceData
{
    /**
     * @throws DataMapperException|ReflectionException
     * @phpstan-ignore-next-line
     */
    public function elementArray(
        DataConfigInterface $dataConfig,
        array $jsonArray,
        null|string $type,
        null|string $destination = null,
    ): ElementArrayInterface {
        $dataList = [];
        $dataType = DataTypeEnum::fromString($type);
        if (class_exists((string) $type)) {
            $dataType = DataTypeEnum::OBJECT;
        }

        if (! $dataType instanceof DataTypeEnum) {
            throw DataMapperException::Error('Element array invalid type');
        }

        foreach ($jsonArray as $jsonKey => $jsonValue) {
            $name = (string) $jsonKey;
            $value = $jsonValue;

            /** ignore phpstan rules, because $dataType has the correct data type */
            $data = match ($dataType) {
                /** @phpstan-ignore argument.type */
                DataTypeEnum::INTEGER => new DataInt($value, $name),
                /** @phpstan-ignore argument.type */
                DataTypeEnum::FLOAT => new DataFloat($value, $name),
                /** @phpstan-ignore argument.type */
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $jsonValue, $type, $name),
                /** @phpstan-ignore cast.string */
                DataTypeEnum::STRING => new DataString((string) $value, $name),
                default => throw DataMapperException::Error('Element array invalid element data type'),
            };

            if ($data === null) {
                continue;
            }

            $dataList[] = $data;
        }

        return new DataArray($dataList, $destination);
    }

    /**
     * @param int|string[] $jsonArray
     * @throws DataMapperException|ReflectionException
     */
    public function elementObject(
        DataConfigInterface $dataConfig,
        array|string|int $jsonArray,
        null|string|object $object,
        null|string $destination = null,
    ): ?ElementObjectInterface {
        $dataList = [];

        if (is_string($object)) {
            $object = $dataConfig->mapClassName($object);
        }

        if (! is_array($jsonArray)) {
            if ($destination === null) {
                return null;
            }

            $jsonArray = (array) $jsonArray;
            $value = array_shift($jsonArray);

            $dataList[] = new DataString((string) $value, $destination);

            return new DataObject($object ?: '', $dataList, $destination, true);
        }

        $objectReflection = $this->reflectionObject($object ?: '');

        foreach ($jsonArray as $jsonKey => $jsonValue) {
            $jsonKey = (string) $jsonKey;
            $value = $jsonValue;

            $childReflection = $objectReflection->find($dataConfig->getApproach(), $jsonKey);
            if (! $childReflection instanceof PropertyReflection) {
                continue;
            }

            $name = $childReflection->getName();
            $dataType = $childReflection->getDataType();
            $targetType = $childReflection->getTargetType();

            /** @phpstan-ignore-next-line */
            if ($childReflection->isNullable() && ($value === null || $value === '')) {
                $dataType = DataTypeEnum::NULL;
            }

            $data = match ($dataType) {
                DataTypeEnum::NULL => new DataNull($name),
                DataTypeEnum::INTEGER => new DataInt($value, $name),
                DataTypeEnum::FLOAT => new DataFloat($value, $name),
                DataTypeEnum::BOOLEAN => new DataBool($value, $name),
                DataTypeEnum::ARRAY => $this->elementArray($dataConfig, (array) $jsonValue, $targetType, $name),
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $jsonValue, $targetType, $name),
                DataTypeEnum::STRING => new DataString((string) $value, $name),
                default => throw DataMapperException::Error('Element object invalid element data type'),
            };

            if ($data === null) {
                continue;
            }

            $dataList[] = $data;
        }

        return new DataObject($object ?: '', $dataList, $destination);
    }

    /**
     * @return T|T[]
     * @throws DataMapperException|ReflectionException
     */
    public function resolve(): object|array
    {
        if (! json_validate($this->source)) {
            throw DataMapperException::InvalidArgument('Invalid JSON string');
        }

        $jsonArray = json_decode($this->source, true);
        if (! is_int($jsonArray) && ! is_string($jsonArray) && ! is_array($jsonArray)) {
            throw DataMapperException::InvalidArgument('Invalid JSON decode return');
        }

        foreach ($this->rootElementTree as $root) {
            /** json_decode give mixed, but all only processed types are already checked above */
            /** @phpstan-ignore offsetAccess.nonOffsetAccessible */
            $jsonArray = $jsonArray[$root] ?? $jsonArray;
        }

        $elementObjectResolver = new ElementObjectResolver();

        /** json_decode give mixed, but all only processed types are already checked above */
        /** @phpstan-ignore argument.type */
        $object = $this->resolveObject($elementObjectResolver, $jsonArray);
        if ($object instanceof $this->object) {
            /** @var T $object */
            return $object;
        }

        $objects = [];
        if (is_iterable($jsonArray)) {
            foreach ($jsonArray ?: [] as $key => $child) {
                if ($child === null) {
                    continue;
                }

                /** @phpstan-ignore argument.type */
                $object = $this->resolveObject($elementObjectResolver, $child);

                if ($object instanceof $this->object) {
                    $objects[$key] = $object;
                }
            }
        }

        if ($this->forceInstance && $objects === []) {
            $object = $elementObjectResolver->createInstance($this->dataConfig, new DataObject($this->object, []));
            if ($object instanceof $this->object) {
                /** @var T $object */
                return $object;
            }
        }

        if ($objects === []) {
            $classString = is_string($this->object) ? $this->object : get_class($this->object);

            throw DataMapperException::Error('Invalid object from JsonResolver, could not create an instance of ' . $classString);
        }

        /** @var T[] $objects */
        return $objects;
    }

    /**
     * @param int|string[] $jsonArray
     * @return null|T
     * @throws DataMapperException|ReflectionException
     */
    private function resolveObject(
        ElementObjectResolver $elementObjectResolver,
        array|string|int $jsonArray,
    ): ?object {
        $elementObject = $this->elementObject($this->dataConfig, $jsonArray, $this->object);
        if (! $elementObject instanceof ElementObjectInterface) {
            return null;
        }

        $object = $elementObjectResolver->resolve($this->dataConfig, $elementObject);

        if (! is_object($object)) {
            return null;
        }

        /** @var T $object */
        return $object;
    }
}
