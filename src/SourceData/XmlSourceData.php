<?php

declare(strict_types=1);

namespace DataMapper\SourceData;

use DataMapper\DataConfig;
use DataMapper\Elements\DataArray;
use DataMapper\Elements\DataBool;
use DataMapper\Elements\DataFloat;
use DataMapper\Elements\DataInt;
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
use SimpleXMLElement;

final class XmlSourceData extends AbstractSourceData
{
    /**
     * @throws Exception
     */
    public function elementArray(
        DataConfig $dataConfig,
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
        DataConfig $dataConfig,
        SimpleXMLElement $xmlElement,
        null|string|object $object,
        null|string $destination = null,
    ): ElementDataInterface {
        $objectReflection = (new ReflectionObjectResolver())->resolve($dataConfig, $object ?: '');
        $dataList = [];

        foreach ($xmlElement->children() as $child) {
            $childReflection = $objectReflection->find($dataConfig->getApproach(), $child->getName());
            if (! $childReflection instanceof PropertyReflection) {
                continue;
            }

            $value = (string) $child;
            $name = $childReflection->getName();

            $dataList[] = match ($childReflection->getDataType()) {
                DataTypeEnum::INTEGER => new DataInt($value, $name),
                DataTypeEnum::FLOAT => new DataFloat($value, $name),
                DataTypeEnum::BOOLEAN => new DataBool($value, $name),
                DataTypeEnum::ARRAY => $this->elementArray($dataConfig, $child, $childReflection->getTargetType(true), $name),
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $child, $childReflection->getTargetType(true), $name),
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
