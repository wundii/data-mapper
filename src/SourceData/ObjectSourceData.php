<?php

declare(strict_types=1);

namespace Wundii\DataMapper\SourceData;

use ReflectionException;
use Wundii\DataMapper\Dto\ReflectionObjectDto;
use Wundii\DataMapper\Dto\PropertyDto;
use Wundii\DataMapper\Dto\Type\ArrayDto;
use Wundii\DataMapper\Dto\Type\BoolDto;
use Wundii\DataMapper\Dto\Type\FloatDto;
use Wundii\DataMapper\Dto\Type\IntDto;
use Wundii\DataMapper\Dto\Type\NullDto;
use Wundii\DataMapper\Dto\Type\ObjectDto;
use Wundii\DataMapper\Dto\Type\StringDto;
use Wundii\DataMapper\Enum\DataTypeEnum;
use Wundii\DataMapper\Enum\SourceTypeEnum;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\ArrayDtoInterface;
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\Interface\ObjectDtoInterface;
use Wundii\DataMapper\Resolver\ObjectDtoResolver;
use Wundii\DataMapper\Resolver\ReflectionObjectResolver;

/**
 * @template T of object
 * @extends AbstractSourceData<T>
 */
final class ObjectSourceData extends AbstractSourceData
{
    public const SOURCE_TYPE = SourceTypeEnum::OBJECT;

    /**
     * @param PropertyDto[] $availableDataList
     * @throws DataMapperException|ReflectionException
     */
    public function elementArray(
        DataConfigInterface $dataConfig,
        array $availableDataList,
        null|string $type,
        null|string $destination = null,
    ): ArrayDtoInterface {
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
            $objectPropertyDto = null;

            if ($dataType === DataTypeEnum::OBJECT) {
                $object = $availableData->getValue();
                if (! is_object($object)) {
                    throw DataMapperException::Error(sprintf('Element array invalid object type for %s', $name));
                }

                $objectPropertyDto = (new ReflectionObjectResolver())->resolve($object, true);
            }

            $data = match ($dataType) {
                DataTypeEnum::INTEGER => new IntDto($value, $name),
                DataTypeEnum::FLOAT => new FloatDto($value, $name),
                DataTypeEnum::OBJECT => $this->objectDto($dataConfig, $objectPropertyDto, $type, $name),
                DataTypeEnum::STRING => new StringDto($value, $name),
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

        return new ArrayDto($dataList, $destination);
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    public function objectDto(
        DataConfigInterface $dataConfig,
        ReflectionObjectDto $objectPropertyDto,
        null|string|object $object,
        null|string $destination = null,
    ): ObjectDtoInterface {
        $dataList = [];

        if (is_string($object)) {
            $object = $dataConfig->mapClassName($object);
        }

        $targetObjectDto = $this->resolveObjectPropertyDto($object ?: '');

        foreach ($objectPropertyDto->availableData() as $availableData) {
            $propertyDto = $targetObjectDto->findPropertyDto($dataConfig->getApproach(), $availableData->getName());
            if (! $propertyDto instanceof PropertyDto) {
                continue;
            }

            $value = $availableData->getStringValue();
            $name = $propertyDto->getName();
            $dataType = $propertyDto->getDataType();
            $targetType = $propertyDto->getTargetType();
            $childPropertyDtos = [];
            $childObjectDto = null;

            if ($propertyDto->isNullable() && $value === '') {
                $dataType = DataTypeEnum::NULL;
            }

            if ($dataType === DataTypeEnum::ARRAY) {
                $childPropertyDtos = $this->arrayToPropertyDtos($availableData);
            }

            if ($dataType === DataTypeEnum::OBJECT) {
                $objectDtoValue = $availableData->getValue();
                if (! is_object($objectDtoValue)) {
                    throw DataMapperException::Error(sprintf('Element array invalid object type for %s', $name));
                }

                $childObjectDto = (new ReflectionObjectResolver())->resolve($objectDtoValue, true);
            }

            $data = match ($dataType) {
                DataTypeEnum::NULL => new NullDto($name),
                DataTypeEnum::INTEGER => new IntDto($value, $name),
                DataTypeEnum::FLOAT => new FloatDto($value, $name),
                DataTypeEnum::BOOLEAN => new BoolDto($value, $name),
                DataTypeEnum::ARRAY => $this->elementArray($dataConfig, $childPropertyDtos, $targetType, $name),
                DataTypeEnum::OBJECT => $this->objectDto($dataConfig, $childObjectDto, $targetType, $name),
                DataTypeEnum::STRING => new StringDto($value, $name),
                default => throw DataMapperException::Error(sprintf('Element object invalid element data type for the target %s', $name)),
            };

            $dataList[] = $data;
        }

        return new ObjectDto($object ?: '', $dataList, $destination);
    }

    /**
     * @return T|T[]
     * @throws DataMapperException|ReflectionException
     */
    public function resolve(): object|array
    {
        $objectDtoResolver = new ObjectDtoResolver();
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

            $objectPropertyDto = (new ReflectionObjectResolver())->resolve($this->source, true);
            $object = $this->resolveObject($objectDtoResolver, $objectPropertyDto);
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
                    $objectPropertyDto = (new ReflectionObjectResolver())->resolve($child, true);
                    $object = $this->resolveObject($objectDtoResolver, $objectPropertyDto);
                }

                if ($object instanceof $this->object) {
                    $objects[$key] = $object;
                }
            }
        }

        if ($this->forceInstance && $objects === []) {
            $object = $objectDtoResolver->createInstance($this->dataConfig, new ObjectDto($this->object, []));
            if ($object instanceof $this->object) {
                /** @var T $object */
                return $object;
            }
        }

        if ($objects === []) {
            $classString = is_string($this->object) ? $this->object : get_class($this->object);

            throw DataMapperException::Error('Invalid object from ObjectResolver, could not create an instance of ' . $classString);
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
     * @return PropertyDto[]
     */
    private function arrayToPropertyDtos(PropertyDto $propertyDto): array
    {
        $propertyDtos = [];

        $array = $propertyDto->getValue();
        if (is_iterable($array)) {
            foreach ($array as $key => $value) {
                $propertyDtos[$key] = new PropertyDto(
                    $propertyDto->getName(),
                    $propertyDto->getDataType(),
                    $propertyDto->getTargetType(),
                    $propertyDto->isNullable(),
                    $propertyDto->getAccessibleEnum(),
                    $propertyDto->isDefaultValueAvailable(),
                    $propertyDto->getDefaultValue(),
                    $value,
                    $propertyDto->getAttributeClassString(),
                );
            }
        }

        return $propertyDtos;
    }

    /**
     * @return null|T
     * @throws DataMapperException|ReflectionException
     */
    private function resolveObject(
        ObjectDtoResolver $objectDtoResolver,
        ReflectionObjectDto $objectPropertyDto,
    ): ?object {
        $objectDto = $this->objectDto($this->dataConfig, $objectPropertyDto, $this->object);
        $object = $objectDtoResolver->resolve($this->dataConfig, $objectDto);

        if (! is_object($object)) {
            return null;
        }

        /** @var T $object */
        return $object;
    }
}
