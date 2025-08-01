<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use stdClass;
use Wundii\DataMapper\Dto\ReflectionObjectDto;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\ArrayDtoInterface;
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\Interface\ObjectDtoInterface;
use Wundii\DataMapper\Interface\TypeDtoInterface;
use Wundii\DataMapper\Interface\ValueDtoInterface;
use Wundii\DataMapper\Parser\ReflectionClassParser;

final class DtoObjectResolver
{
    /**
     * @param mixed[] $parameters
     * @throws DataMapperException|ReflectionException
     */
    public function createInstance(
        DataConfigInterface $dataConfig,
        ObjectDtoInterface $objectDto,
        array $parameters = [],
    ): object {
        $object = $objectDto->getObject();
        $approach = $dataConfig->getApproach();
        $directValue = $objectDto->directValue();

        if (is_object($object)) {
            return $object;
        }

        if ($directValue) {
            if (enum_exists($object)) {
                if (! method_exists($object, 'from')) {
                    throw DataMapperException::Error('Enum class must have a from method: ' . $object);
                }

                /**
                 * $reflectionEnum = new ReflectionEnum(Enum::class);
                 * (string) $reflectionEnum->getBackingType();
                 */
                $parameters = array_map(static fn (mixed $value): mixed => is_numeric($value) ? (int) $value : $value, array_values($parameters));

                $newInstance = $object::from(...$parameters);

                if (! $newInstance instanceof $object) {
                    throw DataMapperException::Error('Enum is not an instance of ' . $object);
                }

                return $newInstance;
            }

            $approach = ApproachEnum::CONSTRUCTOR;
        }

        $parameters = match ($approach) {
            ApproachEnum::CONSTRUCTOR => $parameters,
            default => [],
        };

        /**
         * @todo template T of object by $objectDto->getObject();
         * after that, remove the following if statement
         */
        if (! class_exists($object)) {
            throw DataMapperException::Error('Class does not exist: ' . $object);
        }

        $dataObjectCache = $dataConfig->getDataObjectCache();
        $reflectionClass = new ReflectionClass($object);
        $reflectionObjectDto = $dataObjectCache->getItem($object);
        if (! $reflectionObjectDto instanceof ReflectionObjectDto) {
            $reflectionClassParser = new ReflectionClassParser();
            $reflectionObjectDto = $reflectionClassParser->parse($object);
            $dataObjectCache->save($reflectionObjectDto);
        }

        $requiredParams = $reflectionObjectDto->getNumberOfRequiredConstructProperties();

        if ($approach === ApproachEnum::CONSTRUCTOR) {
            $newInstance = $reflectionClass->newInstanceWithoutConstructor();
            $propertyWasSetByName = false;
            $sortedParameters = [];

            if ($reflectionObjectDto->getPropertiesConst() === []) {
                return new $object(...array_values($parameters));
            }

            /**
             * first level, check how many properties can I set
             */
            $constructorParameters = $requiredParams === 0 || $parameters === []
                ? []
                : $reflectionObjectDto->getPropertiesConst();

            /**
             * second level, to set the values via the properties if level one has released the $parameters
             */
            foreach ($constructorParameters as $constructorParameter) {
                if (
                    ! array_key_exists($constructorParameter->getName(), $parameters)
                    && ! $constructorParameter->isDefaultValueAvailable()
                ) {
                    continue;
                }

                $parameterValue = $parameters[$constructorParameter->getName()] ?? $constructorParameter->getDefaultValue();
                $sortedParameters[$constructorParameter->getName()] = $parameterValue;

                $propertyName = $constructorParameter->getName();
                $currentClass = $reflectionClass;

                while (! $currentClass->hasProperty($propertyName)) {
                    $parentClass = $currentClass->getParentClass();
                    if (! $parentClass instanceof ReflectionClass) {
                        continue;
                    }

                    $currentClass = $parentClass;
                }

                if (! $currentClass->hasProperty($propertyName)) {
                    continue;
                }

                $property = $currentClass->getProperty($propertyName);
                if (! $property->isPublic()) {
                    $property->setAccessible(true);
                }

                $propertyWasSetByName = true;
                $property->setValue($newInstance, $parameterValue);
            }

            if ($propertyWasSetByName) {
                return $newInstance;
            }

            if ($sortedParameters !== []) {
                $parameters = $sortedParameters;
            }

            /**
             * third level, to set the values via the constructor parameters
             * if more parameters are required than passed, then a standard object is returned to indicate that no object could be created
             * a exception is not thrown, because the source data could be a list of objects
             */
            if ($requiredParams > count($parameters)) {
                return new stdClass();
            }

            return new $object(...array_values($parameters));
        }

        if ($requiredParams === 0) {
            return $reflectionClass->newInstance();
        }

        $newInstance = $reflectionClass->newInstanceWithoutConstructor();

        if (
            $approach === ApproachEnum::SETTER
            && $requiredParams > 0
        ) {
            foreach ($reflectionObjectDto->getPropertiesConst() as $propertyDto) {
                $propertyName = $propertyDto->getName();
                $currentClass = $reflectionClass;

                while (! $currentClass->hasProperty($propertyName)) {
                    $parentClass = $currentClass->getParentClass();
                    if (! $parentClass instanceof ReflectionClass) {
                        continue;
                    }

                    $currentClass = $parentClass;
                }

                if (! $currentClass->hasProperty($propertyName)) {
                    continue;
                }

                if (! $propertyDto->isDefaultValueAvailable()) {
                    continue;
                }

                $property = $currentClass->getProperty($propertyName);
                if (! $property->isPublic()) {
                    $property->setAccessible(true);
                }

                if (! $property->isReadOnly()) {
                    $property->setValue($newInstance, $propertyDto->getDefaultValue());
                }
            }
        }

        return $newInstance;
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    public function matchValue(
        DataConfigInterface $dataConfig,
        TypeDtoInterface $typeDto,
    ): mixed {
        $dtoArrayResolver = new DtoArrayResolver();
        $dtoValueResolver = new DtoValueResolver();

        return match (true) {
            $typeDto instanceof ArrayDtoInterface => $dtoArrayResolver->resolve($dataConfig, $typeDto),
            $typeDto instanceof ObjectDtoInterface => $this->resolve($dataConfig, $typeDto),
            $typeDto instanceof ValueDtoInterface => $dtoValueResolver->resolve($typeDto),
            default => throw DataMapperException::Error('TypeDtoInterface not implemented: ' . $typeDto::class),
        };
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    public function resolve(
        DataConfigInterface $dataConfig,
        ObjectDtoInterface $objectDto,
    ): ?object {
        return match ($dataConfig->getApproach()) {
            ApproachEnum::CONSTRUCTOR => $this->constructor($dataConfig, $objectDto),
            ApproachEnum::PROPERTY => $this->properties($dataConfig, $objectDto),
            ApproachEnum::SETTER => $this->setters($dataConfig, $objectDto),
        };
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    private function constructor(
        DataConfigInterface $dataConfig,
        ObjectDtoInterface $objectDto,
    ): object {
        if ($dataConfig->getApproach() === ApproachEnum::CONSTRUCTOR && is_object($objectDto->getObject())) {
            throw DataMapperException::Error('You can not use constructor approach with an object');
        }

        $parameters = [];

        foreach ($objectDto->getValue() as $typeDto) {
            $parameters[$typeDto->getDestination()] = $this->matchValue($dataConfig, $typeDto);
        }

        return $this->createInstance($dataConfig, $objectDto, $parameters);
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    private function properties(
        DataConfigInterface $dataConfig,
        ObjectDtoInterface $objectDto,
    ): ?object {
        $setValues = $objectDto->directValue();
        $instance = $this->constructor($dataConfig, $objectDto);

        foreach ($objectDto->getValue() as $typeDto) {
            $destination = $typeDto->getDestination();

            if ($destination === null) {
                throw DataMapperException::Error('Destination is not declared');
            }

            if (! property_exists($instance, $destination)) {
                continue;
            }

            $setValues = true;
            $value = $this->matchValue($dataConfig, $typeDto);

            if ($dataConfig->getAccessible() === AccessibleEnum::PRIVATE) {
                $reflectionProperty = new ReflectionProperty($instance, $destination);
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($instance, $value);
                continue;
            }

            $instance->{$destination} = $value;
        }

        return $setValues ? $instance : null;
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    private function setters(
        DataConfigInterface $dataConfig,
        ObjectDtoInterface $objectDto,
    ): ?object {
        $setValues = $objectDto->directValue();
        $instance = $this->constructor($dataConfig, $objectDto);

        foreach ($objectDto->getValue() as $typeDto) {
            $destination = $typeDto->getDestination();
            if ($destination === null) {
                throw DataMapperException::Error('Destination is not declared');
            }

            if (! method_exists($instance, $destination)) {
                continue;
            }

            $setValues = true;
            $value = $this->matchValue($dataConfig, $typeDto);

            if ($dataConfig->getAccessible() === AccessibleEnum::PRIVATE) {
                $reflectionMethod = new ReflectionMethod($instance, $destination);
                $reflectionMethod->setAccessible(true);
                $reflectionMethod->invoke($instance, $value);
                continue;
            }

            $instance->{$destination}($value);
        }

        return $setValues ? $instance : null;
    }
}
