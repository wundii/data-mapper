<?php

declare(strict_types=1);

namespace Wundii\DataMapper\SourceData;

use ReflectionException;
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
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\Interface\ElementDtoInterface;
use Wundii\DataMapper\Interface\ObjectDtoInterface;
use Wundii\DataMapper\Resolver\DtoObjectResolver;

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
    ): ArrayDto {
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
                /** @phpstan-ignore-next-line argument.type */
                DataTypeEnum::INTEGER => new IntDto($value, $name),
                /** @phpstan-ignore-next-line argument.type */
                DataTypeEnum::FLOAT => new FloatDto($value, $name),
                /** @phpstan-ignore-next-line  argument.type */
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $value, $type, $name),
                /** @phpstan-ignore-next-line cast.string */
                DataTypeEnum::STRING => new StringDto((string) $value, $name),
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
     * @param int|string[] $array
     * @param class-string<T>|T|null $objectOrClass
     * @throws DataMapperException|ReflectionException
     */
    public function elementObject(
        DataConfigInterface $dataConfig,
        array|string|int|null $array,
        null|string|object $objectOrClass,
        null|string $destination = null,
    ): ?ObjectDtoInterface {
        $dataList = [];

        if (is_string($objectOrClass)) {
            $objectOrClass = $dataConfig->mapClassName($objectOrClass);
        }

        if (! is_array($array)) {
            if ($destination === null) {
                return null;
            }

            $array = (array) $array;
            $value = array_shift($array);

            $dataList[] = new StringDto((string) $value, $destination);

            return new ObjectDto($objectOrClass ?: '', $dataList, $destination, true);
        }

        /** @phpstan-ignore-next-line  */
        $reflectionObjectDto = $this->resolveObjectDto($objectOrClass ?: '');

        foreach ($array as $arrayKey => $arrayValue) {
            $arrayKey = (string) $arrayKey;
            $value = $arrayValue;

            $elementDto = $reflectionObjectDto->findElementDto($dataConfig->getApproach(), $arrayKey);
            if (! $elementDto instanceof ElementDtoInterface) {
                continue;
            }

            $name = $elementDto->getName();
            $dataType = $elementDto->getDataType();
            $targetType = $elementDto->getTargetType();

            /** @phpstan-ignore-next-line */
            if ($elementDto->isNullable() && ($value === null || $value === '')) {
                $dataType = DataTypeEnum::NULL;
            }

            $data = match ($dataType) {
                DataTypeEnum::NULL => new NullDto($name),
                DataTypeEnum::INTEGER => new IntDto($value, $name),
                DataTypeEnum::FLOAT => new FloatDto($value, $name),
                DataTypeEnum::BOOLEAN => new BoolDto($value, $name),
                DataTypeEnum::ARRAY => $this->elementArray($dataConfig, (array) $arrayValue, $targetType, $name),
                /** @phpstan-ignore-next-line argument.type */
                DataTypeEnum::OBJECT => $this->elementObject($dataConfig, $arrayValue, $targetType, $name),
                DataTypeEnum::STRING => new StringDto((string) $value, $name),
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
    public function resolve(?SourceTypeEnum $sourceTypeEnum = null): object|array
    {
        $dtoObjectResolver = new DtoObjectResolver();
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
        $object = $this->resolveObject($dtoObjectResolver, $array);
        if ($object instanceof $this->objectOrClass) {
            /** @var T $object */
            return $object;
        }

        $objects = [];
        foreach ($array ?: [] as $key => $child) {
            if ($child === null) {
                continue;
            }

            /** @phpstan-ignore argument.type */
            $object = $this->resolveObject($dtoObjectResolver, $child);

            if ($object instanceof $this->objectOrClass) {
                $objects[$key] = $object;
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
        DtoObjectResolver $dtoObjectResolver,
        array|string|int|null $array,
    ): ?object {
        $elementObject = $this->elementObject($this->dataConfig, $array, $this->objectOrClass);
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
