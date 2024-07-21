<?php

declare(strict_types=1);

namespace DataMapper\Resolver;

use DataMapper\DataConfig;
use DataMapper\Elements\DataArray;
use DataMapper\Elements\DataObject;
use DataMapper\Enum\ApproachEnum;
use DataMapper\Interface\ObjectElementInterface;
use Exception;
use InvalidArgumentException;

final readonly class ObjectElementResolver
{
    public function __construct(
        private DataConfig $dataConfig,
        private ObjectElementInterface $dataObjectElement,
    ) {
    }

    public function objectName(): string
    {
        $objectName = $this->dataObjectElement->getObjectName();

        if (! class_exists($objectName) && ! interface_exists($objectName)) {
            throw new InvalidArgumentException(sprintf('Class %s not found', $objectName));
        }

        return $this->dataConfig->mapClassName($objectName);
    }

    /**
     * @param mixed[] $parameter
     */
    public function createInstanceFromString(string $objectName, array $parameter = []): object
    {
        return new $objectName(...$parameter);
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
        $parameter = [];

        foreach ($this->dataObjectElement->getValue() as $dataElement) {
            $value = match (get_class($dataElement)) {
                DataArray::class => (new ArrayElementResolver($this->dataConfig, $dataElement))->resolve(),
                DataObject::class => (new self($this->dataConfig, $dataElement))->resolve(),
                default => $dataElement->getValue(),
            };

            $parameter[] = $value;
        }

        return $this->createInstanceFromString($this->objectName(), $parameter);
    }

    /**
     * @throws Exception
     */
    private function properties(): object
    {
        $instance = $this->createInstanceFromString($this->objectName());

        foreach ($this->dataObjectElement->getValue() as $dataElement) {
            $destination = $dataElement->getDestination();

            if ($destination === null) {
                throw new Exception('Destination is not declared');
            }

            if (! property_exists($instance, $destination)) {
                // throw new Exception(sprintf('Property %s not found', $destination));
                continue;
            }

            $value = match (get_class($dataElement)) {
                DataArray::class => (new ArrayElementResolver($this->dataConfig, $dataElement))->resolve(),
                DataObject::class => (new self($this->dataConfig, $dataElement))->resolve(),
                default => $dataElement->getValue(),
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
        $instance = $this->createInstanceFromString($this->objectName());

        foreach ($this->dataObjectElement->getValue() as $dataElement) {
            $destination = $dataElement->getDestination();

            if ($destination === null) {
                throw new Exception('Destination is not declared');
            }

            if (! method_exists($instance, $destination)) {
                // throw new Exception(sprintf('Method %s not found', $destination));
                continue;
            }

            $value = match (get_class($dataElement)) {
                DataArray::class => (new ArrayElementResolver($this->dataConfig, $dataElement))->resolve(),
                DataObject::class => (new self($this->dataConfig, $dataElement))->resolve(),
                default => $dataElement->getValue(),
            };

            $instance->{$destination}($value);
        }

        return $instance;
    }
}
