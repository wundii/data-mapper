<?php

declare(strict_types=1);

namespace Unit\Reflection;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Reflection\AnnotationReflection;
use Wundii\DataMapper\Reflection\ParameterReflection;

class AnnotationReflectionTest extends TestCase
{
    public function testIsEmpty(): void
    {
        $annotationReflection = new AnnotationReflection([], []);

        $this->assertTrue($annotationReflection->isEmpty());
    }

    public function testIsNotEmpty(): void
    {
        $annotationReflection = new AnnotationReflection(
            [
                new ParameterReflection('name', ['string']),
            ],
            [
                'string',
            ]
        );

        $this->assertFalse($annotationReflection->isEmpty());

        $annotationReflection = new AnnotationReflection(
            [
                new ParameterReflection('name', ['string']),
            ],
            []
        );

        $this->assertFalse($annotationReflection->isEmpty());

        $annotationReflection = new AnnotationReflection(
            [],
            [
                'string',
            ]
        );

        $this->assertFalse($annotationReflection->isEmpty());
    }
}
