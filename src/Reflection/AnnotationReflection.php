<?php

declare(strict_types=1);

namespace DataMapper\Reflection;

final readonly class AnnotationReflection
{
    public function __construct(
        private ParameterReflection $parameterReflection,
    ) {
    }

    public function getParameterReflection(): ParameterReflection
    {
        return $this->parameterReflection;
    }
}
