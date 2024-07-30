<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use Wundii\DataMapper\Reflection\AnnotationReflection;
use Wundii\DataMapper\Reflection\PropertyReflection;

final readonly class PropertyReflectionResolver
{
    public function resolve(
        string $name,
        array $types,
        AnnotationReflection $annotationReflection,
    ): PropertyReflection
    {


        return new PropertyReflection(
            $name,
            $types,
            $annotationReflection,
        );
    }
}
