<?php

declare(strict_types=1);

namespace Unit\Dto;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Dto\AnnotationDto;
use Wundii\DataMapper\Dto\ParameterDto;

class AnnotationDtoTest extends TestCase
{
    public function testIsEmpty(): void
    {
        $annotationReflection = new AnnotationDto([], []);

        $this->assertTrue($annotationReflection->isEmpty());
    }

    public function testIsNotEmpty(): void
    {
        $annotationReflection = new AnnotationDto(
            [
                new ParameterDto('name', ['string']),
            ],
            [
                'string',
            ]
        );

        $this->assertFalse($annotationReflection->isEmpty());

        $annotationReflection = new AnnotationDto(
            [
                new ParameterDto('name', ['string']),
            ],
            []
        );

        $this->assertFalse($annotationReflection->isEmpty());

        $annotationReflection = new AnnotationDto(
            [],
            [
                'string',
            ]
        );

        $this->assertFalse($annotationReflection->isEmpty());
    }
}
