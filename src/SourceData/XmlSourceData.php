<?php

declare(strict_types=1);

namespace Wundii\DataMapper\SourceData;

use Exception;
use ReflectionException;
use SimpleXMLElement;
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
use Wundii\DataMapper\Interface\ElementDtoInterface;
use Wundii\DataMapper\Interface\ObjectDtoInterface;
use Wundii\DataMapper\Resolver\DtoObjectResolver;

/**
 * @template T of object
 * @extends AbstractSourceData<T>
 */
final class XmlSourceData extends AbstractSourceData
{
    public const SOURCE_TYPE = SourceTypeEnum::XML;

    /**
     * @throws DataMapperException|ReflectionException
     */
    public function elementArray(
        DataConfigInterface $dataConfig,
        SimpleXMLElement $xmlElement,
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

        foreach ($xmlElement->children() as $child) {
            $name = $child->getName();
            $value = (string) $child;

            $data = match ($dataType) {
                DataTypeEnum::INTEGER => new IntDto($value, $name),
                DataTypeEnum::FLOAT => new FloatDto($value, $name),
                /** @phpstan-ignore-next-line argument.type */
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $child, $type, $name),
                DataTypeEnum::STRING => new StringDto($value, $name),
                default => throw DataMapperException::Error('Element array invalid element data type for the target ' . $name),
            };

            if ($data === null) {
                continue;
            }

            $dataList[] = $data;
        }

        return new ArrayDto($dataList, $destination);
    }

    /**
     * @param class-string<T>|T|null $objectOrClass
     * @throws DataMapperException|ReflectionException
     */
    public function elementObject(
        DataConfigInterface $dataConfig,
        SimpleXMLElement $xmlElement,
        null|string|object $objectOrClass,
        null|string $destination = null,
    ): ?ObjectDtoInterface {
        $dataList = [];

        if (is_string($objectOrClass)) {
            $objectOrClass = $dataConfig->mapClassName($objectOrClass);
        }

        if ($xmlElement->count() === 0) {
            if ($destination === null) {
                return null;
            }

            $value = (string) $xmlElement;
            $dataList[] = new StringDto($value, $destination);

            return new ObjectDto($objectOrClass ?: '', $dataList, $destination, true);
        }

        /** @phpstan-ignore-next-line */
        $reflectionObjectDto = $this->resolveObjectDto($objectOrClass ?: '');

        foreach ($xmlElement->children() as $child) {
            $elementDto = $reflectionObjectDto->findElementDto($dataConfig->getApproach(), $child->getName());
            if (! $elementDto instanceof ElementDtoInterface) {
                continue;
            }

            $value = (string) $child;
            $name = $elementDto->getName();
            $dataType = $elementDto->getDataType();
            $targetType = $elementDto->getTargetType();

            if ($elementDto->isNullable() && $value === '') {
                $dataType = DataTypeEnum::NULL;
            }

            $data = match ($dataType) {
                DataTypeEnum::NULL => new NullDto($name),
                DataTypeEnum::INTEGER => new IntDto($value, $name),
                DataTypeEnum::FLOAT => new FloatDto($value, $name),
                DataTypeEnum::BOOLEAN => new BoolDto($value, $name),
                DataTypeEnum::ARRAY => $this->elementArray($dataConfig, $child, $targetType, $name),
                /** @phpstan-ignore-next-line */
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $child, $targetType, $name),
                DataTypeEnum::STRING => new StringDto($value, $name),
                default => throw DataMapperException::Error('Element object invalid element data type for the target ' . $name),
            };

            if ($data === null) {
                continue;
            }

            $dataList[] = $data;
        }

        return new ObjectDto($objectOrClass ?: '', $dataList, $destination);
    }

    /**
     * @return T|T[]
     * @throws DataMapperException|ReflectionException
     */
    public function resolve(): object|array
    {
        $dtoObjectResolver = new DtoObjectResolver();
        $sourceType = self::SOURCE_TYPE;

        if (! is_string($this->source) && ! $this->source instanceof SimpleXMLElement) {
            throw DataMapperException::Error(sprintf('The %s source is not a string or SimpleXmlElement', $sourceType->value));
        }

        if ($this->source instanceof SimpleXMLElement) {
            $xmlElement = $this->source;
        } else {
            try {
                $xmlElement = new SimpleXmlElement($this->source);
            } catch (Exception $exception) {
                throw DataMapperException::Error(sprintf('Invalid %s: %s', $sourceType->value, $exception->getMessage()), (int) $exception->getCode(), $exception);
            }
        }

        foreach ($this->rootElementTree as $rootElement) {
            $found = false;
            foreach ($xmlElement->children() as $child) {
                if (strcasecmp($child->getName(), $rootElement) === 0) {
                    $xmlElement = $child;
                    $found = true;
                    break;
                }
            }

            if (! $found && ! $this->forceInstance) {
                throw DataMapperException::Error(sprintf('Root-Element "%s" not found in %s source data, you can use the forceInstance option to create an empty instance.', $rootElement, $sourceType->value));
            }
        }

        $object = $this->resolveObject($dtoObjectResolver, $xmlElement);
        if ($object instanceof $this->objectOrClass) {
            /** @var T $object */
            return $object;
        }

        $objects = [];
        foreach ($xmlElement->children() ?? [] as $child) {
            $object = $this->resolveObject($dtoObjectResolver, $child);
            if ($object instanceof $this->objectOrClass) {
                $objects[] = $object;
            }
        }

        if ($this->forceInstance && $objects === []) {
            $object = $dtoObjectResolver->createInstance($this->dataConfig, new ObjectDto($this->objectOrClass, []));
            if ($object instanceof $this->objectOrClass) {
                /** @var T $object */
                return $object;
            }
        }

        if ($objects === []) {
            $classString = is_string($this->objectOrClass) ? $this->objectOrClass : get_class($this->objectOrClass);

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
        DtoObjectResolver $dtoObjectResolver,
        SimpleXMLElement $xmlElement,
    ): ?object {
        $elementObject = $this->elementObject($this->dataConfig, $xmlElement, $this->objectOrClass);
        if (! $elementObject instanceof ObjectDtoInterface) {
            return null;
        }

        $object = $dtoObjectResolver->resolve($this->dataConfig, $elementObject);

        if (! is_object($object)) {
            return null;
        }

        /** @var T $object */
        return $object;
    }
}
