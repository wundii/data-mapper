<?php

declare(strict_types=1);

namespace DataMapper\Reflection;

final readonly class AnnotationReflection
{
    /**
     * @param ParameterReflection[] $parameterReflections
     */
    public function __construct(
        private array $parameterReflections,
    ) {
    }

    /**
     * @return ParameterReflection[]
     */
    public function getParameterReflections(): array
    {
        return $this->parameterReflections;
    }
}
