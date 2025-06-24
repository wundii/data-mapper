<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

final readonly class ParameterDto
{
    /**
     * @param string[] $types
     */
    public function __construct(
        private string $parameter,
        private array $types,
        private bool $isDefaultValueAvailable = false,
        private mixed $defaultValue = null,
    ) {
    }

    public function getParameter(): string
    {
        return $this->parameter;
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    public function isDefaultValueAvailable(): bool
    {
        return $this->isDefaultValueAvailable;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }
}
