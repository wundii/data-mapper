<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\MethodTypeEnum;

final readonly class MethodDto
{
    /**
     * @param ParameterDto[] $parameters
     * @param AttributeDto[] $attributes
     */
    public function __construct(
        private MethodTypeEnum $methodTypeEnum,
        private AccessibleEnum $accessibleEnum,
        private string $name,
        private ?string $returnType = null,
        private ?AnnotationDto $annotations = null,
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

    public function getReturnType(): ?string
    {
        return $this->returnType;
    }

    public function getAnnotations(): AnnotationDto
    {
        return $this->annotations;
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