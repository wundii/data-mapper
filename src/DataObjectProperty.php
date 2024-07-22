<?php

declare(strict_types=1);

namespace DataMapper;

final readonly class DataObjectProperty
{
    /**
     * @param string[] $properties
     * @param string[] $setters
     */
    public function __construct(
        private array $properties,
        private array $setters,
    ) {
    }

    /**
     * @return string[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return string[]
     */
    public function getSetters(): array
    {
        return $this->setters;
    }
}
