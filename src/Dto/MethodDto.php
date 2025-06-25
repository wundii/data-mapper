<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\MethodTypeEnum;

final readonly class MethodDto
{
    /**
     * @param string[] $returnTypes
     * @param ParameterDto[] $parameters
     * @param AttributeDto[] $attributes
     */
    public function __construct(
        private MethodTypeEnum $methodTypeEnum,
        private AccessibleEnum $accessibleEnum,
        private string $name,
        private mixed $value = null,
        private array $returnTypes = [],
        private ?AnnotationDto $annotationDto = null,
        private array $parameters = [],
        private array $attributes = [],
    ) {
    }

    public function getMethodTypeEnum(): MethodTypeEnum
    {
        return $this->methodTypeEnum;
    }

    public function getAccessibleEnum(): AccessibleEnum
    {
        return $this->accessibleEnum;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getReturnTypes(): array
    {
        return $this->returnTypes;
    }

    public function getAnnotationDto(): AnnotationDto
    {
        return $this->annotationDto;
    }

    /**
     * @return ParameterDto[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return AttributeDto[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
