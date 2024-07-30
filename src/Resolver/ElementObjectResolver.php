<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Wundii\DataMapper\Elements\DataArray;
use Wundii\DataMapper\Elements\DataObject;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\Interface\ElementDataInterface;
use Wundii\DataMapper\Interface\ElementObjectInterface;

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
                $parameter = array_map(static fn (mixed $value): mixed => is_numeric($value) ? (int) $value : $value, $parameter);

                return $object::from(...$parameter);
            }

            $approach = ApproachEnum::CONSTRUCTOR;
        }

        $parameter = match ($approach) {
            ApproachEnum::CONSTRUCTOR => $parameter,
            default => [],
        };

        if ($approach === ApproachEnum::CONSTRUCTOR) {
            return new $object(...$parameter);
        }

        /**
         * @todo template T of object by $elementObject->getObject();
         * after that, remove the following if statement
         */
        if (! class_exists($object)) {
            throw DataMapperException::Error('Class does not exist: ' . $object);
        }

        $reflectionClass = new ReflectionClass($object);
        $constructor = $reflectionClass->getConstructor();

        if (
            $constructor instanceof ReflectionMethod
            && $constructor->getNumberOfRequiredParameters() === 0
        ) {
            return $reflectionClass->newInstance();
        }

        return $reflectionClass->newInstanceWithoutConstructor();
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

        return match (get_class($elementData)) {
            DataArray::class => $elementArrayResolver->resolve($dataConfig, $elementData),
            DataObject::class => $this->resolve($dataConfig, $elementData),
            default => $elementValueResolver->resolve($elementData),
        };
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    public function resolve(
        DataConfigInterface $dataConfig,
        ElementObjectInterface $elementObject,
    ): object {
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
            $parameter[] = $this->matchValue($dataConfig, $elementData);
        }

        return $this->createInstance($dataConfig, $elementObject, $parameter);
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    private function properties(
        DataConfigInterface $dataConfig,
        ElementObjectInterface $elementObject,
    ): object {
        $instance = $this->constructor($dataConfig, $elementObject);

        foreach ($elementObject->getValue() as $elementData) {
            $destination = $elementData->getDestination();

            if ($destination === null) {
                throw DataMapperException::Error('Destination is not declared');
            }

            if (! property_exists($instance, $destination)) {
                continue;
            }

            $value = $this->matchValue($dataConfig, $elementData);

            $instance->{$destination} = $value;
        }

        return $instance;
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    private function setters(
        DataConfigInterface $dataConfig,
        ElementObjectInterface $elementObject,
    ): object {
        $instance = $this->constructor($dataConfig, $elementObject);

        foreach ($elementObject->getValue() as $elementData) {
            $destination = $elementData->getDestination();

            if ($destination === null) {
                throw DataMapperException::Error('Destination is not declared');
            }

            if (! method_exists($instance, $destination)) {
                continue;
            }

            $value = $this->matchValue($dataConfig, $elementData);

            $instance->{$destination}($value);
        }

        return $instance;
    }
}
