<?php

declare(strict_types=1);

namespace Wundii\DataMapper\SourceData;

use Exception;
use ReflectionException;
use SimpleXMLElement;
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

final class XmlSourceData extends AbstractSourceData
{
    /**
     * @throws DataMapperException|ReflectionException
     */
    public function elementArray(
        DataConfigInterface $dataConfig,
        SimpleXMLElement $xmlElement,
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

        foreach ($xmlElement->children() as $child) {
            $name = $child->getName();
            $value = (string) $child;

            $dataList[] = match ($dataType) {
                DataTypeEnum::INTEGER => new DataInt($value, $name),
                DataTypeEnum::FLOAT => new DataFloat($value, $name),
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $child, $type, $name),
                DataTypeEnum::STRING => new DataString($value, $name),
                default => throw DataMapperException::Error('Element array invalid element data type'),
            };
        }

        return new DataArray($dataList, $destination);
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    public function elementObject(
        DataConfigInterface $dataConfig,
        SimpleXMLElement $xmlElement,
        null|string|object $object,
        null|string $destination = null,
    ): ElementDataInterface {
        $dataList = [];

        if (is_string($object)) {
            $object = $dataConfig->mapClassName($object);
        }

        if ($xmlElement->count() === 0) {
            $value = (string) $xmlElement;
            $dataList[] = new DataString($value, $destination);

            return new DataObject($object ?: '', $dataList, $destination, true);
        }

        $objectReflection = $this->reflectionObject($object ?: '');

        foreach ($xmlElement->children() as $child) {
            $childReflection = $objectReflection->find($dataConfig->getApproach(), $child->getName());
            if (! $childReflection instanceof PropertyReflection) {
                continue;
            }

            $value = (string) $child;
            $name = $childReflection->getName();
            $dataType = $childReflection->getDataType();
            $targetType = $childReflection->getTargetType(true);

            if ($childReflection->isNullable() && $value === '') {
                $dataType = DataTypeEnum::NULL;
            }

            $dataList[] = match ($dataType) {
                DataTypeEnum::NULL => new DataNull($name),
                DataTypeEnum::INTEGER => new DataInt($value, $name),
                DataTypeEnum::FLOAT => new DataFloat($value, $name),
                DataTypeEnum::BOOLEAN => new DataBool($value, $name),
                DataTypeEnum::ARRAY => $this->elementArray($dataConfig, $child, $targetType, $name),
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $child, $targetType, $name),
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
        try {
            $xmlElement = new SimpleXmlElement($this->source);
        } catch (Exception $exception) {
            throw DataMapperException::Error('Invalid XML: ' . $exception->getMessage(), (int) $exception->getCode(), $exception);
        }

        foreach ($this->customRoot as $root) {
            $xmlElement = $xmlElement->$root;
        }

        $elementData = $this->elementObject($this->dataConfig, $xmlElement, $this->object);
        if (! $elementData instanceof ElementObjectInterface) {
            throw DataMapperException::Error('Invalid ElementDataInterface from from XmlResolver');
        }

        return (new ElementObjectResolver())->resolve($this->dataConfig, $elementData);
    }
}
