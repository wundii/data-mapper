<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

final readonly class AnnotationDto
{
    /**
     * @param ParameterDto[] $parameterDtos
     * @param string[] $variables
     */
    public function __construct(
        private array $parameterDtos,
        private array $variables,
    ) {
    }

    /**
     * @return ParameterDto[]
     */
    public function getParameterDto(): array
    {
        return $this->parameterDtos;
    }

    /**
     * @return string[]
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    public function isEmpty(): bool
    {
        return $this->parameterDtos === [] && $this->variables === [];
    }
}
