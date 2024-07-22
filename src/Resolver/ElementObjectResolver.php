<?php

declare(strict_types=1);

namespace DataMapper\Resolver;

use DataMapper\DataConfig;
use DataMapper\Elements\DataArray;
use DataMapper\Elements\DataObject;
use DataMapper\Enum\ApproachEnum;
use DataMapper\Interface\ElementObjectInterface;
use Exception;

final readonly class ElementObjectResolver
{
    public function __construct(
        private DataConfig $dataConfig,
        private ElementObjectInterface $elementObject,
    ) {
    }

    public function object(): string|object
    {
        $object = $this->elementObject->getObject();

        if (is_object($object)) {
            return $object;
        }

        return $this->dataConfig->mapClassName($object);
    }

    /**
     * @param mixed[] $parameter
     */
    public function createInstance(string|object $object, array $parameter = []): object
    {
        if (is_object($object)) {
            return $object;
        }

        return new $object(...$parameter);
    }

    /**
     * @throws Exception
     */
    public function resolve(): object
    {
        return match ($this->dataConfig->getApproach()) {
            ApproachEnum::CONSTRUCTOR => $this->constructor(),
            ApproachEnum::PROPERTY => $this->properties(),
            ApproachEnum::SETTER => $this->setters(),
        };
    }

    /**
     * @throws Exception
     */
    private function constructor(): object
    {
        if (is_object($this->object())) {
            throw new Exception('You can not use constructor approach with an object');
        }

        $parameter = [];

        foreach ($this->elementObject->getValue() as $elementData) {
            $value = match (get_class($elementData)) {
                DataArray::class => (new ElementArrayResolver($this->dataConfig, $elementData))->resolve(),
                DataObject::class => (new self($this->dataConfig, $elementData))->resolve(),
                default => $elementData->getValue(),
            };

            $parameter[] = $value;
        }

        return $this->createInstance($this->object(), $parameter);
    }

    /**
     * @throws Exception
     */
    private function properties(): object
    {
        $instance = $this->createInstance($this->object());

        foreach ($this->elementObject->getValue() as $elementData) {
            $destination = $elementData->getDestination();

            if ($destination === null) {
                throw new Exception('Destination is not declared');
            }

            if (! property_exists($instance, $destination)) {
                continue;
            }

            $value = match (get_class($elementData)) {
                DataArray::class => (new ElementArrayResolver($this->dataConfig, $elementData))->resolve(),
                DataObject::class => (new self($this->dataConfig, $elementData))->resolve(),
                default => $elementData->getValue(),
            };

            $instance->{$destination} = $value;
        }

        return $instance;
    }

    /**
     * @throws Exception
     */
    private function setters(): object
    {
        $instance = $this->createInstance($this->object());

        foreach ($this->elementObject->getValue() as $elementData) {
            $destination = $elementData->getDestination();

            if ($destination === null) {
                throw new Exception('Destination is not declared');
            }

            if (! method_exists($instance, $destination)) {
                continue;
            }

            $value = match (get_class($elementData)) {
                DataArray::class => (new ElementArrayResolver($this->dataConfig, $elementData))->resolve(),
                DataObject::class => (new self($this->dataConfig, $elementData))->resolve(),
                default => $elementData->getValue(),
            };

            $instance->{$destination}($value);
        }

        return $instance;
    }
}
