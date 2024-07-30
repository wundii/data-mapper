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
use Wundii\DataMapper\Interface\ElementDataInterface;
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

            $dataList[] = match ($dataType) {
                DataTypeEnum::INTEGER => new DataInt($value, $name),
                DataTypeEnum::FLOAT => new DataFloat($value, $name),
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $jsonValue, $type, $name),
                DataTypeEnum::STRING => new DataString($value, $name),
                default => throw DataMapperException::Error('Element array invalid element data type'),
            };
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
    ): ElementDataInterface {
        $dataList = [];

        if (is_string($object)) {
            $object = $dataConfig->mapClassName($object);
        }

        if (! is_array($jsonArray)) {
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
            $targetType = $childReflection->getTargetType(true);

            /** @phpstan-ignore-next-line */
            if ($childReflection->isNullable() && ($value === null || $value === '')) {
                $dataType = DataTypeEnum::NULL;
            }

            $dataList[] = match ($dataType) {
                DataTypeEnum::NULL => new DataNull($name),
                DataTypeEnum::INTEGER => new DataInt($value, $name),
                DataTypeEnum::FLOAT => new DataFloat($value, $name),
                DataTypeEnum::BOOLEAN => new DataBool($value, $name),
                DataTypeEnum::ARRAY => $this->elementArray($dataConfig, (array) $jsonValue, $targetType, $name),
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $jsonValue, $targetType, $name),
                default => new DataString($value, $name),
            };
        }

        return new DataObject($object ?: '', $dataList, $destination);
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    public function resolve(): object
    {
        if (! json_validate($this->source)) {
            throw DataMapperException::InvalidArgument('Invalid JSON string');
        }

        $jsonArray = json_decode($this->source, true);
        if (! is_int($jsonArray) && ! is_string($jsonArray) && ! is_array($jsonArray)) {
            throw DataMapperException::InvalidArgument('Invalid JSON decode return');
        }

        foreach ($this->rootElementTree as $root) {
            $jsonArray = $jsonArray[$root] ?? $jsonArray;
        }

        $elementData = $this->elementObject($this->dataConfig, $jsonArray, $this->object);
        if (! $elementData instanceof ElementObjectInterface) {
            throw DataMapperException::Error('Invalid ElementDataInterface from JsonResolver');
        }

        return (new ElementObjectResolver())->resolve($this->dataConfig, $elementData);
    }
}
