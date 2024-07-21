<?php

declare(strict_types=1);

namespace DataMapper\Elements;

use DataMapper\Interface\DataElementInterface;
use DataMapper\Interface\ObjectElementInterface;
use InvalidArgumentException;

final readonly class DataObject implements ObjectElementInterface
{
    /**
     * @param DataElementInterface[] $value
     */
    public function __construct(
        private string $objectName,
        private array $value,
        private ?string $destination = null,
    ) {
        if (! class_exists($objectName) && ! interface_exists($objectName)) {
            throw new InvalidArgumentException(sprintf('object %s does not exist', $objectName));
        }
    }

    public function getObjectName(): string
    {
        return $this->objectName;
    }

    /**
     * @return DataElementInterface[]
     */
    public function getValue(): array
    {
        return $this->value;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }
}
