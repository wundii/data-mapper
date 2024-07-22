<?php

declare(strict_types=1);

namespace DataMapper\Resolver;

use DataMapper\DataConfig;
use DataMapper\Elements\DataArray;
use DataMapper\Elements\DataObject;
use DataMapper\Enum\ApproachEnum;
use DataMapper\Interface\ElementDataInterface;
use DataMapper\Interface\ElementObjectInterface;
use Exception;

final readonly class ElementObjectResolver
{
    /**
     * @param mixed[] $parameter
     */
    public function createInstance(
        DataConfig $dataConfig,
        ElementObjectInterface $elementObject,
        array $parameter = [],
    ): object {
        $object = $elementObject->getObject();

        if (is_object($object)) {
            return $object;
        }

        $object = $dataConfig->mapClassName($object);

        return new $object(...$parameter);
    }

    /**
     * @throws Exception
     */
    public function matchValue(
        DataConfig $dataConfig,
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
     * @throws Exception
     */
    public function resolve(
        DataConfig $dataConfig,
        ElementObjectInterface $elementObject,
    ): object {
        return match ($dataConfig->getApproach()) {
            ApproachEnum::CONSTRUCTOR => $this->constructor($dataConfig, $elementObject),
            ApproachEnum::PROPERTY => $this->properties($dataConfig, $elementObject),
            ApproachEnum::SETTER => $this->setters($dataConfig, $elementObject),
        };
    }

    /**
     * @throws Exception
     */
    private function constructor(
        DataConfig $dataConfig,
        ElementObjectInterface $elementObject,
    ): object {
        if (is_object($elementObject->getObject())) {
            throw new Exception('You can not use constructor approach with an object');
        }

        $parameter = [];

        foreach ($elementObject->getValue() as $elementData) {
            $parameter[] = $this->matchValue($dataConfig, $elementData);
        }

        return $this->createInstance($dataConfig, $elementObject, $parameter);
    }

    /**
     * @throws Exception
     */
    private function properties(
        DataConfig $dataConfig,
        ElementObjectInterface $elementObject,
    ): object {
        $instance = $this->createInstance($dataConfig, $elementObject);

        foreach ($elementObject->getValue() as $elementData) {
            $destination = $elementData->getDestination();

            if ($destination === null) {
                throw new Exception('Destination is not declared');
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
     * @throws Exception
     */
    private function setters(
        DataConfig $dataConfig,
        ElementObjectInterface $elementObject,
    ): object {
        $instance = $this->createInstance($dataConfig, $elementObject);

        foreach ($elementObject->getValue() as $elementData) {
            $destination = $elementData->getDestination();

            if ($destination === null) {
                throw new Exception('Destination is not declared');
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