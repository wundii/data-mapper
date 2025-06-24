<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

class AttributeDto
{
    /**
     * @param AttributePropertyDto[] $attributeProperties
     */
    public function __construct(
        private string $classString,
        private array $attributeProperties = [],
    ) {
    }

    public function getClassString(): string
    {
        return $this->classString;
    }

    /**
     * @return AttributePropertyDto[]
     */
    public function getAttributeProperties(): array
    {
        return $this->attributeProperties;
    }
}