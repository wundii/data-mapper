<?php

declare(strict_types=1);

namespace DataMapper\SourceData;

use DataMapper\Elements\DataArray;
use DataMapper\Elements\DataBool;
use DataMapper\Elements\DataFloat;
use DataMapper\Elements\DataInt;
use DataMapper\Elements\DataNull;
use DataMapper\Elements\DataObject;
use DataMapper\Elements\DataString;
use DataMapper\Enum\DataTypeEnum;
use DataMapper\Interface\DataConfigInterface;
use DataMapper\Interface\ElementArrayInterface;
use DataMapper\Interface\ElementDataInterface;
use DataMapper\Interface\ElementObjectInterface;
use DataMapper\Reflection\PropertyReflection;
use DataMapper\Resolver\ElementObjectResolver;
use Exception;
use SimpleXMLElement;

final class XmlSourceData extends AbstractSourceData
{
    /**
     * @throws Exception
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
            throw new Exception('Element array invalid type');
        }

        foreach ($xmlElement->children() as $child) {
            $name = $child->getName();
            $value = (string) $child;

            $dataList[] = match ($dataType) {
                DataTypeEnum::INTEGER => new DataInt($value, $name),
                DataTypeEnum::FLOAT => new DataFloat($value, $name),
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $child, $type, $name),
                DataTypeEnum::STRING => new DataString($value, $name),
                default => throw new Exception('Element array invalid element data type'),
            };
        }

        return new DataArray($dataList, $destination);
    }

    /**
     * @throws Exception
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
     * @throws Exception
     */
    public function resolve(): object
    {
        try {
            $xmlElement = new SimpleXmlElement($this->source);
        } catch (Exception $exception) {
            throw new Exception('Invalid XML: ' . $exception->getMessage(), (int) $exception->getCode(), $exception);
        }

        $elementData = $this->elementObject($this->dataConfig, $xmlElement, $this->object);
        if (! $elementData instanceof ElementObjectInterface) {
            throw new Exception('Invalid ElementDataInterface');
        }

        return (new ElementObjectResolver())->resolve($this->dataConfig, $elementData);
    }
}
