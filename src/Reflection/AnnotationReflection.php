<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Reflection;

final readonly class AnnotationReflection
{
    /**
     * @param ParameterReflection[] $parameterReflections
     * @param string[] $variables
     */
    public function __construct(
        private array $parameterReflections,
        private array $variables,
    ) {
    }

    /**
     * @return ParameterReflection[]
     */
    public function getParameterReflections(): array
    {
        return $this->parameterReflections;
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
        return $this->parameterReflections === [] && $this->variables === [];
    }
}
