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
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\Interface\ElementArrayInterface;
use Wundii\DataMapper\Interface\ElementDataInterface;
use Wundii\DataMapper\Interface\ElementObjectInterface;
use Wundii\DataMapper\Interface\ElementValueInterface;

final readonly class ElementObjectResolver
{
    /**
     * @param mixed[] $parameter
     * @throws DataMapperException|ReflectionException
     */
    public function createInstance(
        DataConfigInterface $dataConfig,
        ElementObjectInterface $elementObject,
        array $parameter = [],
    ): object {
        $object = $elementObject->getObject();
        $approach = $dataConfig->getApproach();
        $directValue = $elementObject->directValue();

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
         * @todo template T of object by $elementObject->getObject();
         * after that, remove the following if statement
         */
        if (! class_exists($object)) {
            throw DataMapperException::Error('Class does not exist: ' . $object);
        }

        $reflectionClass = new ReflectionClass($object);
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

                $reflection = new ReflectionClass(get_class($newInstance));
                $property = $reflection->getProperty($instanceParameter->getName());
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
        ElementDataInterface $elementData,
    ): mixed {
        $elementArrayResolver = new ElementArrayResolver();
        $elementValueResolver = new ElementValueResolver();

        return match (true) {
            $elementData instanceof ElementArrayInterface => $elementArrayResolver->resolve($dataConfig, $elementData),
            $elementData instanceof ElementObjectInterface => $this->resolve($dataConfig, $elementData),
            $elementData instanceof ElementValueInterface => $elementValueResolver->resolve($elementData),
            default => throw DataMapperException::Error('ElementInterface not implemented: ' . $elementData::class),
        };
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    public function resolve(
        DataConfigInterface $dataConfig,
        ElementObjectInterface $elementObject,
    ): ?object {
        return match ($dataConfig->getApproach()) {
            ApproachEnum::CONSTRUCTOR => $this->constructor($dataConfig, $elementObject),
            ApproachEnum::PROPERTY => $this->properties($dataConfig, $elementObject),
            ApproachEnum::SETTER => $this->setters($dataConfig, $elementObject),
        };
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    private function constructor(
        DataConfigInterface $dataConfig,
        ElementObjectInterface $elementObject,
    ): object {
        if ($dataConfig->getApproach() === ApproachEnum::CONSTRUCTOR && is_object($elementObject->getObject())) {
            throw DataMapperException::Error('You can not use constructor approach with an object');
        }

        $parameter = [];

        foreach ($elementObject->getValue() as $elementData) {
            $parameter[$elementData->getDestination()] = $this->matchValue($dataConfig, $elementData);
        }

        return $this->createInstance($dataConfig, $elementObject, $parameter);
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    private function properties(
        DataConfigInterface $dataConfig,
        ElementObjectInterface $elementObject,
    ): ?object {
        $setValues = $elementObject->directValue();
        $instance = $this->constructor($dataConfig, $elementObject);

        foreach ($elementObject->getValue() as $elementData) {
            $destination = $elementData->getDestination();

            if ($destination === null) {
                throw DataMapperException::Error('Destination is not declared');
            }

            if (! property_exists($instance, $destination)) {
                continue;
            }

            $setValues = true;
            $value = $this->matchValue($dataConfig, $elementData);

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
        ElementObjectInterface $elementObject,
    ): ?object {
        $setValues = $elementObject->directValue();
        $instance = $this->constructor($dataConfig, $elementObject);

        foreach ($elementObject->getValue() as $elementData) {
            $destination = $elementData->getDestination();
            if ($destination === null) {
                throw DataMapperException::Error('Destination is not declared');
            }

            if (! method_exists($instance, $destination)) {
                continue;
            }

            $setValues = true;
            $value = $this->matchValue($dataConfig, $elementData);

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
