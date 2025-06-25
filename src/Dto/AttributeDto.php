<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

use Wundii\DataMapper\Enum\AttributeOriginEnum;

final readonly class AttributeDto
{
    /**
     * @param AttributePropertyDto[] $attributeProperties
     */
    public function __construct(
        private AttributeOriginEnum $attributeOriginEnum,
        private string $originName,
        private string $classString,
        private array $attributeProperties = [],
    ) {
    }

    public function getAttributeOriginEnum(): AttributeOriginEnum
    {
        return $this->attributeOriginEnum;
    }

    public function getOriginName(): string
    {
        return $this->originName;
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
