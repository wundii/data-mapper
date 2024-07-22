<?php

declare(strict_types=1);

namespace DataMapper\Reflection;

final readonly class ParameterReflection
{
    /**
     * @param string[] $types
     */
    public function __construct(
        private string $parameter,
        private array $types,
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
}
