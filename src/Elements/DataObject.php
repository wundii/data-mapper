<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Elements;

use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\ElementDataInterface;
use Wundii\DataMapper\Interface\ElementObjectInterface;

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
            throw DataMapperException::InvalidArgument(sprintf('object %s does not exist', $object));
        }
    }

    public function __toString(): string
    {
        if (is_object($this->object)) {
            return get_class($this->object);
        }

        return $this->object;
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
