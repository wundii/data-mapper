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
use Wundii\DataMapper\Reflection\ObjectReflection;
use Wundii\DataMapper\Reflection\PropertyReflection;
use Wundii\DataMapper\Resolver\ElementObjectResolver;
use Wundii\DataMapper\Resolver\ReflectionObjectResolver;

/**
 * @template T of object
 * @extends AbstractSourceData<T>
 */
final class ObjectSourceData extends AbstractSourceData
{
    public const SOURCE_TYPE = SourceTypeEnum::OBJECT;

    /**
     * @param PropertyReflection[] $availableDataList
     * @throws DataMapperException|ReflectionException
     */
    public function elementArray(
        DataConfigInterface $dataConfig,
        array $availableDataList,
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

        foreach ($availableDataList as $availableData) {
            $name = $availableData->getName();
            $value = $availableData->getStringValue();
            $objectResolve = null;

            if ($dataType === DataTypeEnum::OBJECT) {
                $object = $availableData->getValue();
                if (! is_object($object)) {
                    throw DataMapperException::Error(sprintf('Element array invalid object type for %s', $name));
                }

                $objectResolve = (new ReflectionObjectResolver())->resolve($object, true);
            }

            $data = match ($dataType) {
                DataTypeEnum::INTEGER => new DataInt($value, $name),
                DataTypeEnum::FLOAT => new DataFloat($value, $name),
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $objectResolve, $type, $name),
                DataTypeEnum::STRING => new DataString($value, $name),
                default => throw DataMapperException::Error(sprintf('Element array invalid element data type %s for the target %s', $type, $name)),
            };

            /**
             * Skip objects with empty data in the array.
             */
            if ($dataType === DataTypeEnum::OBJECT && $data->getValue() === []) {
                continue;
            }

            $dataList[] = $data;
        }

        return new DataArray($dataList, $destination);
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    public function elementObject(
        DataConfigInterface $dataConfig,
        ObjectReflection $objectReflection,
        null|string|object $object,
        null|string $destination = null,
    ): ElementObjectInterface {
        $dataList = [];

        if (is_string($object)) {
            $object = $dataConfig->mapClassName($object);
        }

        $objectReflectionTarget = $this->reflectionObject($object ?: '');

        foreach ($objectReflection->availableData() as $availableData) {
            $childReflection = $objectReflectionTarget->find($dataConfig->getApproach(), $availableData->getName());
            if (! $childReflection instanceof PropertyReflection) {
                continue;
            }

            $value = $availableData->getStringValue();
            $name = $childReflection->getName();
            $dataType = $childReflection->getDataType();
            $targetType = $childReflection->getTargetType();
            $arrayData = [];
            $objectResolve = null;

            if ($childReflection->isNullable() && $value === '') {
                $dataType = DataTypeEnum::NULL;
            }

            if ($dataType === DataTypeEnum::ARRAY) {
                $arrayData = $this->arrayToPropertyReflectionList($availableData);
            }

            if ($dataType === DataTypeEnum::OBJECT) {
                $objectTarget = $availableData->getValue();
                if (! is_object($objectTarget)) {
                    throw DataMapperException::Error(sprintf('Element array invalid object type for %s', $name));
                }

                $objectResolve = (new ReflectionObjectResolver())->resolve($objectTarget, true);
            }

            $data = match ($dataType) {
                DataTypeEnum::NULL => new DataNull($name),
                DataTypeEnum::INTEGER => new DataInt($value, $name),
                DataTypeEnum::FLOAT => new DataFloat($value, $name),
                DataTypeEnum::BOOLEAN => new DataBool($value, $name),
                DataTypeEnum::ARRAY => $this->elementArray($dataConfig, $arrayData, $targetType, $name),
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $objectResolve, $targetType, $name),
                DataTypeEnum::STRING => new DataString($value, $name),
                default => throw DataMapperException::Error(sprintf('Element object invalid element data type for the target %s', $name)),
            };

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
        $elementObjectResolver = new ElementObjectResolver();
        $sourceTypeEnum = self::SOURCE_TYPE;

        if (! is_array($this->source) && ! is_object($this->source)) {
            throw DataMapperException::Error(sprintf('The %s source is not a array or object', $sourceTypeEnum->value));
        }

        if (is_object($this->source)) {
            $object = $this->toArray($this->source);
            if ($object instanceof $this->object) {
                /** @var T $object */
                return $object;
            }

            $objectResolve = (new ReflectionObjectResolver())->resolve($this->source, true);
            $object = $this->resolveObject($elementObjectResolver, $objectResolve);
            if ($object instanceof $this->object) {
                /** @var T $object */
                return $object;
            }
        }

        $objects = [];
        if (is_array($this->source)) {
            foreach ($this->source as $key => $child) {
                if (! is_object($child)) {
                    continue;
                }

                $object = $this->toArray($child);
                if ($object === null) {
                    $objectResolve = (new ReflectionObjectResolver())->resolve($child, true);
                    $object = $this->resolveObject($elementObjectResolver, $objectResolve);
                }

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

            throw DataMapperException::Error('Invalid object from XmlResolver, could not create an instance of ' . $classString);
        }

        /** @var T[] $objects */
        return $objects;
    }

    /**
     * @return null|T|T[]
     * @throws ReflectionException
     * @throws DataMapperException
     */
    private function toArray(object $object): null|object|array
    {
        if (! method_exists($object, 'toArray')) {
            return null;
        }

        /** @var array<int|string, mixed> $array */
        $array = $object->toArray();

        $arraySourceData = new ArraySourceData(
            $this->dataConfig,
            $array,
            $this->object,
            $this->rootElementTree,
            $this->forceInstance,
        );

        return $arraySourceData->resolve(self::SOURCE_TYPE);
    }

    /**
     * @return PropertyReflection[]
     */
    private function arrayToPropertyReflectionList(PropertyReflection $propertyReflection): array
    {
        $propertyReflections = [];

        $array = $propertyReflection->getValue();
        if (is_iterable($array)) {
            foreach ($array as $key => $value) {
                $propertyReflections[$key] = new PropertyReflection(
                    $propertyReflection->getName(),
                    $propertyReflection->getDataType(),
                    $propertyReflection->getTargetType(),
                    $propertyReflection->isOneType(),
                    $propertyReflection->isNullable(),
                    $propertyReflection->getVisibilityEnum(),
                    $value,
                );
            }
        }

        return $propertyReflections;
    }

    /**
     * @return null|T
     * @throws DataMapperException|ReflectionException
     */
    private function resolveObject(
        ElementObjectResolver $elementObjectResolver,
        ObjectReflection $objectReflection,
    ): ?object {
        $elementObject = $this->elementObject($this->dataConfig, $objectReflection, $this->object);
        $object = $elementObjectResolver->resolve($this->dataConfig, $elementObject);

        if (! is_object($object)) {
            return null;
        }

        /** @var T $object */
        return $object;
    }
}
