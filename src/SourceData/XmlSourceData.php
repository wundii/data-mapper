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
use Wundii\DataMapper\Interface\ElementObjectInterface;
use Wundii\DataMapper\Reflection\PropertyReflection;
use Wundii\DataMapper\Resolver\ElementObjectResolver;

/**
 * @template T of object
 * @extends AbstractSourceData<T>
 */
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

            $data = match ($dataType) {
                DataTypeEnum::INTEGER => new DataInt($value, $name),
                DataTypeEnum::FLOAT => new DataFloat($value, $name),
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $child, $type, $name),
                DataTypeEnum::STRING => new DataString($value, $name),
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
     * @throws DataMapperException|ReflectionException
     */
    public function elementObject(
        DataConfigInterface $dataConfig,
        SimpleXMLElement $xmlElement,
        null|string|object $object,
        null|string $destination = null,
    ): ?ElementObjectInterface {
        $dataList = [];

        if (is_string($object)) {
            $object = $dataConfig->mapClassName($object);
        }

        if ($xmlElement->count() === 0) {
            if ($destination === null) {
                return null;
            }

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
            $targetType = $childReflection->getTargetType();

            if ($childReflection->isNullable() && $value === '') {
                $dataType = DataTypeEnum::NULL;
            }

            $data = match ($dataType) {
                DataTypeEnum::NULL => new DataNull($name),
                DataTypeEnum::INTEGER => new DataInt($value, $name),
                DataTypeEnum::FLOAT => new DataFloat($value, $name),
                DataTypeEnum::BOOLEAN => new DataBool($value, $name),
                DataTypeEnum::ARRAY => $this->elementArray($dataConfig, $child, $targetType, $name),
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $child, $targetType, $name),
                DataTypeEnum::STRING => new DataString($value, $name),
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
        try {
            $xmlElement = new SimpleXmlElement($this->source);
        } catch (Exception $exception) {
            throw DataMapperException::Error('Invalid XML: ' . $exception->getMessage(), (int) $exception->getCode(), $exception);
        }

        foreach ($this->rootElementTree as $root) {
            $xmlElement = $xmlElement->{$root};
        }

        if (! $xmlElement instanceof SimpleXMLElement) {
            throw DataMapperException::Error('Invalid XML element');
        }

        $elementObjectResolver = new ElementObjectResolver();

        $object = $this->resolveObject($elementObjectResolver, $xmlElement);
        if ($object instanceof $this->object) {
            /** @var T $object */
            return $object;
        }

        $objects = [];
        foreach ($xmlElement->children() ?? [] as $child) {
            $object = $this->resolveObject($elementObjectResolver, $child);
            if ($object instanceof $this->object) {
                $objects[] = $object;
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
     * @return null|T
     * @throws DataMapperException|ReflectionException
     */
    private function resolveObject(
        ElementObjectResolver $elementObjectResolver,
        SimpleXMLElement $xmlElement,
    ): ?object {
        $elementObject = $this->elementObject($this->dataConfig, $xmlElement, $this->object);
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
