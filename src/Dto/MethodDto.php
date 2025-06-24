<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

use Wundii\DataMapper\Enum\AccessibleEnum;

class MethodDto
{
    /**
     * @param ParameterDto[] $parameters
     * @param AnnotationDto[] $annotations
     * @param AttributeDto[] $attributes
     */
    public function __construct(
        private AccessibleEnum $accessibleEnum,
        private string $name,
        private ?string $returnType = null,
        private array $parameters = [],
        private array $annotations = [],
        private array $attributes = [],
    ) {
    }

    public function getAccessibleEnum(): AccessibleEnum
    {
        return $this->accessibleEnum;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReturnType(): ?string
    {
        return $this->returnType;
    }

    /**
     * @return ParameterDto[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return AnnotationDto[]
     */
    public function getAnnotations(): array
    {
        return $this->annotations;
    }

    /**
     * @return AttributeDto[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}