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
use Wundii\DataMapper\Enum\SourceTypeEnum;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\Interface\ElementArrayInterface;
use Wundii\DataMapper\Interface\ElementObjectInterface;
use Wundii\DataMapper\Reflection\PropertyReflection;
use Wundii\DataMapper\Resolver\ElementObjectResolver;

/**
 * @template T of object
 * @extends AbstractSourceData<T>
 */
final class ArraySourceData extends AbstractSourceData
{
    public const SOURCE_TYPE = SourceTypeEnum::ARRAY;

    /**
     * @param array<int|string, mixed> $array
     * @throws DataMapperException|ReflectionException
     */
    public function elementArray(
        DataConfigInterface $dataConfig,
        array $array,
        null|string $type,
        null|string $destination = null,
    ): ElementArrayInterface {
        $dataList = [];
        $dataType = DataTypeEnum::fromString($type);
        if (class_exists((string) $type)) {
            $dataType = DataTypeEnum::OBJECT;
        }

        if (! $dataType instanceof DataTypeEnum) {
            throw DataMapperException::Error(sprintf('Element array invalid element data type %s for the target %s', $type, $destination));
        }

        foreach ($array as $arrayKey => $arrayValue) {
            $name = (string) $arrayKey;
            $value = $arrayValue;

            /** ignore phpstan rules, because $dataType has the correct data type */
            $data = match ($dataType) {
                /** @phpstan-ignore argument.type */
                DataTypeEnum::INTEGER => new DataInt($value, $name),
                /** @phpstan-ignore argument.type */
                DataTypeEnum::FLOAT => new DataFloat($value, $name),
                /** @phpstan-ignore argument.type */
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $arrayValue, $type, $name),
                /** @phpstan-ignore cast.string */
                DataTypeEnum::STRING => new DataString((string) $value, $name),
                default => throw DataMapperException::Error('Element array invalid element data type for the target ' . $name),
            };

            if ($data === null) {
                continue;
            }

            $dataList[] = $data;
        }

        return new DataArray($dataList, $destination);
    }

    /**
     * @param int|string[] $array
     * @throws DataMapperException|ReflectionException
     */
    public function elementObject(
        DataConfigInterface $dataConfig,
        array|string|int|null $array,
        null|string|object $object,
        null|string $destination = null,
    ): ?ElementObjectInterface {
        $dataList = [];

        if (is_string($object)) {
            $object = $dataConfig->mapClassName($object);
        }

        if (! is_array($array)) {
            if ($destination === null) {
                return null;
            }

            $array = (array) $array;
            $value = array_shift($array);

            $dataList[] = new DataString((string) $value, $destination);

            return new DataObject($object ?: '', $dataList, $destination, true);
        }

        $objectReflection = $this->reflectionObject($object ?: '');

        foreach ($array as $arrayKey => $arrayValue) {
            $arrayKey = (string) $arrayKey;
            $value = $arrayValue;

            $childReflection = $objectReflection->find($dataConfig->getApproach(), $arrayKey);
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
                DataTypeEnum::ARRAY => $this->elementArray($dataConfig, (array) $arrayValue, $targetType, $name),
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $arrayValue, $targetType, $name),
                DataTypeEnum::STRING => new DataString((string) $value, $name),
                default => throw DataMapperException::Error('Element object invalid element data type for the target ' . $name),
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
    public function resolve(?SourceTypeEnum $sourceTypeEnum = null): object|array
    {
        $elementObjectResolver = new ElementObjectResolver();
        $sourceTypeEnum = $sourceTypeEnum ?? self::SOURCE_TYPE;

        $array = $this->source;

        if (! is_array($array)) {
            throw DataMapperException::Error(sprintf('The %s source is not a %s', $sourceTypeEnum->value, $sourceTypeEnum->value));
        }

        foreach ($this->rootElementTree as $rootElement) {
            $found = false;
            foreach (array_keys($array) as $key) {
                if (strcasecmp((string) $key, $rootElement) === 0) {
                    $array = (array) $array[$key];
                    $found = true;
                    break;
                }
            }

            if (! $found && ! $this->forceInstance) {
                throw DataMapperException::Error(sprintf('Root-Element "%s" not found in %s source data, you can use the forceInstance option to create an empty instance.', $rootElement, $sourceTypeEnum->value));
            }
        }

        /** @phpstan-ignore argument.type */
        $object = $this->resolveObject($elementObjectResolver, $array);
        if ($object instanceof $this->object) {
            /** @var T $object */
            return $object;
        }

        $objects = [];
        foreach ($array ?: [] as $key => $child) {
            if ($child === null) {
                continue;
            }

            /** @phpstan-ignore argument.type */
            $object = $this->resolveObject($elementObjectResolver, $child);

            if ($object instanceof $this->object) {
                $objects[$key] = $object;
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

            throw DataMapperException::Error(sprintf('Invalid object from %sResolver, could not create an instance of %s', $sourceTypeEnum->value, $classString));
        }

        /** @var T[] $objects */
        return $objects;
    }

    /**
     * @param int|string[] $array
     * @return null|T
     * @throws DataMapperException|ReflectionException
     */
    private function resolveObject(
        ElementObjectResolver $elementObjectResolver,
        array|string|int|null $array,
    ): ?object {
        $elementObject = $this->elementObject($this->dataConfig, $array, $this->object);
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
