<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use stdClass;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\ArrayDtoInterface;
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\Interface\ObjectDtoInterface;
use Wundii\DataMapper\Interface\TypeDtoInterface;
use Wundii\DataMapper\Interface\ValueDtoInterface;

final class ObjectDtoResolver extends AbstractReflectionClassResolver
{
    /**
     * @param mixed[] $parameter
     * @throws DataMapperException|ReflectionException
     */
    public function createInstance(
        DataConfigInterface $dataConfig,
        ObjectDtoInterface $objectDto,
        array $parameter = [],
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
                $parameter = array_map(static fn (mixed $value): mixed => is_numeric($value) ? (int) $value : $value, array_values($parameter));

                $newInstance = $object::from(...$parameter);

                if (! $newInstance instanceof $object) {
                    throw DataMapperException::Error('Enum is not an instance of ' . $object);
                }

                return $newInstance;
            }

            $approach = ApproachEnum::CONSTRUCTOR;
        }

        $parameter = match ($approach) {
            ApproachEnum::CONSTRUCTOR => $parameter,
            default => [],
        };

        /**
         * @todo template T of object by $objectDto->getObject();
         * after that, remove the following if statement
         */
        if (! class_exists($object)) {
            throw DataMapperException::Error('Class does not exist: ' . $object);
        }

        $reflectionClass = $this->reflectionClassCache($object);
        $constructor = $reflectionClass->getConstructor();

        if ($approach === ApproachEnum::CONSTRUCTOR) {
            $newInstance = $reflectionClass->newInstanceWithoutConstructor();
            $propertyWasSetByName = false;
            $parameters = [];

            if (! $constructor instanceof ReflectionMethod) {
                return new $object(...array_values($parameter));
            }

            /**
             * first level, check how many properties can I set
             */
            foreach ($constructor->getParameters() as $instanceParameter) {
                if (! array_key_exists($instanceParameter->getName(), $parameter)) {
                    continue;
                }

                $parameters = $constructor->getParameters();
                break;
            }

            /**
             * second level, to set the values via the properties if level one has released the $parameters
             */
            foreach ($parameters as $instanceParameter) {
                if (
                    ! array_key_exists($instanceParameter->getName(), $parameter)
                    && ! $instanceParameter->isDefaultValueAvailable()
                ) {
                    continue;
                }

                $setValue = $parameter[$instanceParameter->getName()] ?? $instanceParameter->getDefaultValue();

                $property = $reflectionClass->getProperty($instanceParameter->getName());
                if (! $property->isPublic()) {
                    $property->setAccessible(true);
                }

                $propertyWasSetByName = true;
                $property->setValue($newInstance, $setValue);
            }

            if ($propertyWasSetByName) {
                return $newInstance;
            }

            /**
             * third level, to set the values via the constructor parameters
             * if more parameters are required than passed, then a standard object is returned to indicate that no object could be created
             * a exception is not thrown, because the source data could be a list of objects
             */
            $requiredParams = $constructor->getNumberOfRequiredParameters();
            if ($requiredParams > count($parameter)) {
                return new stdClass();
            }

            return new $object(...array_values($parameter));
        }

        if (
            $constructor instanceof ReflectionMethod
            && $constructor->getNumberOfRequiredParameters() === 0
        ) {
            return $reflectionClass->newInstance();
        }

        $newInstance = $reflectionClass->newInstanceWithoutConstructor();

        if (
            $approach === ApproachEnum::SETTER
            && $constructor instanceof ReflectionMethod
            && $constructor->getNumberOfRequiredParameters() > 0
        ) {
            foreach ($constructor->getParameters() as $instanceParameter) {
                if (! $instanceParameter->isDefaultValueAvailable()) {
                    continue;
                }

                $reflectionClass = $this->reflectionClassCache($newInstance);
                $property = $reflectionClass->getProperty($instanceParameter->getName());
                if (! $property->isPublic()) {
                    $property->setAccessible(true);
                }

                if (! $property->isReadOnly()) {
                    $property->setValue($newInstance, $instanceParameter->getDefaultValue());
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
        $arrayDtoResolver = new ArrayDtoResolver();
        $valueDtoResolver = new ValueDtoResolver();

        return match (true) {
            $typeDto instanceof ArrayDtoInterface => $arrayDtoResolver->resolve($dataConfig, $typeDto),
            $typeDto instanceof ObjectDtoInterface => $this->resolve($dataConfig, $typeDto),
            $typeDto instanceof ValueDtoInterface => $valueDtoResolver->resolve($typeDto),
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

        $parameter = [];

        foreach ($objectDto->getValue() as $typeDto) {
            $parameter[$typeDto->getDestination()] = $this->matchValue($dataConfig, $typeDto);
        }

        return $this->createInstance($dataConfig, $objectDto, $parameter);
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
