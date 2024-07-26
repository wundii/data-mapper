<?php

declare(strict_types=1);

namespace DataMapper\SourceData;

use DataMapper\DataConfig;
use DataMapper\Elements\DataArray;
use DataMapper\Elements\DataBool;
use DataMapper\Elements\DataFloat;
use DataMapper\Elements\DataInt;
use DataMapper\Elements\DataNull;
use DataMapper\Elements\DataObject;
use DataMapper\Elements\DataString;
use DataMapper\Enum\DataTypeEnum;
use DataMapper\Interface\ElementArrayInterface;
use DataMapper\Interface\ElementDataInterface;
use DataMapper\Interface\ElementObjectInterface;
use DataMapper\Reflection\PropertyReflection;
use DataMapper\Resolver\ElementObjectResolver;
use DataMapper\Resolver\ReflectionObjectResolver;
use Exception;
use InvalidArgumentException;

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
     * @throws Exception
     * @phpstan-ignore-next-line
     */
    public function elementArray(
        DataConfig $dataConfig,
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
            throw new Exception('Element array invalid type');
        }

        foreach ($jsonArray as $jsonKey => $jsonValue) {
            $name = (string) $jsonKey;
            $value = $jsonValue;

            $dataList[] = match ($dataType) {
                DataTypeEnum::INTEGER => new DataInt($value, $name),
                DataTypeEnum::FLOAT => new DataFloat($value, $name),
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $jsonValue, $type, $name),
                DataTypeEnum::STRING => new DataString($value, $name),
                default => throw new Exception('Element array invalid element data type'),
            };
        }

        return new DataArray($dataList, $destination);
    }

    /**
     * @param int|string[] $jsonArray
     * @throws Exception
     */
    public function elementObject(
        DataConfig $dataConfig,
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

        $objectReflection = (new ReflectionObjectResolver())->resolve($object ?: '');

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
     * @throws Exception
     */
    public function resolve(): object
    {
        if (! json_validate($this->source)) {
            throw new InvalidArgumentException('Invalid JSON string');
        }

        $jsonArray = json_decode($this->source, true);
        if (! is_int($jsonArray) && ! is_string($jsonArray) && ! is_array($jsonArray)) {
            throw new InvalidArgumentException('Invalid JSON decode return');
        }

        $elementData = $this->elementObject($this->dataConfig, $jsonArray, $this->object);
        if (! $elementData instanceof ElementObjectInterface) {
            throw new Exception('Invalid ElementDataInterface');
        }

        return (new ElementObjectResolver())->resolve($this->dataConfig, $elementData);
    }
}
