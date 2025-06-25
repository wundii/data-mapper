<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

final readonly class AttributePropertyDto
{
    public function __construct(
        private string $name,
        private string $value,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}