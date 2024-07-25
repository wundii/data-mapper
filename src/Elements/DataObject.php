<?php

declare(strict_types=1);

namespace DataMapper\Elements;

use DataMapper\Interface\ElementDataInterface;
use DataMapper\Interface\ElementObjectInterface;
use InvalidArgumentException;

final readonly class DataObject implements ElementObjectInterface
{
    /**
     * @param ElementDataInterface[] $value
     */
    public function __construct(
        private string|object $object,
        private array $value,
        private ?string $destination = null,
        private bool $directValue = false,
    ) {
        if (! is_object($object) && ! class_exists($object) && ! interface_exists($object)) {
            throw new InvalidArgumentException(sprintf('object %s does not exist', $object));
        }
    }

    public function getObject(): string|object
    {
        return $this->object;
    }

    /**
     * @return ElementDataInterface[]
     */
    public function getValue(): array
    {
        return $this->value;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function directValue(): bool
    {
        return $this->directValue;
    }
}
