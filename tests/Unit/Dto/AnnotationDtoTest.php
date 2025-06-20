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
        $annotationDto = new AnnotationDto([], []);

        $this->assertTrue($annotationDto->isEmpty());
    }

    public function testIsNotEmpty(): void
    {
        $annotationDto = new AnnotationDto(
            [
                new ParameterDto('name', ['string']),
            ],
            [
                'string',
            ]
        );

        $this->assertFalse($annotationDto->isEmpty());

        $annotationDto = new AnnotationDto(
            [
                new ParameterDto('name', ['string']),
            ],
            []
        );

        $this->assertFalse($annotationDto->isEmpty());

        $annotationDto = new AnnotationDto(
            [],
            [
                'string',
            ]
        );

        $this->assertFalse($annotationDto->isEmpty());
    }
}
