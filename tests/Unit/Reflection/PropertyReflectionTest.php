<?php

declare(strict_types=1);

namespace DataMapper\Tests\Unit\Reflection;

use DataMapper\Reflection\AnnotationReflection;
use DataMapper\Reflection\ParameterReflection;
use DataMapper\Reflection\PropertyReflection;
use PHPUnit\Framework\TestCase;

class PropertyReflectionTest extends TestCase
{
    public function annotationEmpty(): AnnotationReflection
    {
        return new AnnotationReflection([], []);
    }

    public function annotationSimple(): AnnotationReflection
    {
        return new AnnotationReflection(
            [new ParameterReflection('name', ['string'])],
            ['string'],
        );
    }

    public function annotationComplex(): AnnotationReflection
    {
        return new AnnotationReflection(
            [
                new ParameterReflection('name', ['string', 'null', 'float']),
                new ParameterReflection('data', ['array']),
                new ParameterReflection('item', ['DataMapper\Tests\MockClasses\ItemConstructor']),
            ],
            ['bool', 'string', 'DataMapper\Tests\MockClasses\ItemConstructor[]', 'DataMapper\Tests\MockClasses\RootConstructor'],
        );
    }

    public function annotationParameter(): AnnotationReflection
    {
        return new AnnotationReflection(
            [
                new ParameterReflection('name', ['string', 'null', 'float']),
                new ParameterReflection('data', ['array']),
                new ParameterReflection('item', ['DataMapper\Tests\MockClasses\ItemConstructor']),
            ],
            [],
        );
    }

    public function testTypesEmpty(): void
    {
        $property = new PropertyReflection(
            'name',
            ['string'],
            $this->annotationEmpty(),
        );

        $this->assertSame('name', $property->getName());
        $this->assertSame(['string'], $property->getTypes());
    }

    public function testTypesSimple(): void
    {
        $property = new PropertyReflection(
            'name',
            ['string'],
            $this->annotationSimple(),
        );

        $this->assertSame('name', $property->getName());
        $this->assertSame(['string'], $property->getTypes());
    }

    public function testTypesComplex(): void
    {
        $property = new PropertyReflection(
            'name',
            ['string'],
            $this->annotationComplex(),
        );

        $expected = [
            'string',
            'bool',
            'DataMapper\Tests\MockClasses\ItemConstructor[]',
            'DataMapper\Tests\MockClasses\RootConstructor',
            'null',
            'float',
        ];

        $this->assertSame($expected, $property->getTypes());
    }

    public function testClassStringNull(): void
    {
        $property = new PropertyReflection(
            'name',
            ['string'],
            $this->annotationSimple(),
        );

        $this->assertNull($property->getClassString());

        $property = new PropertyReflection(
            'findMeIfYouCan',
            ['string'],
            $this->annotationParameter(),
        );

        $this->assertNull($property->getClassString());
    }

    public function testClassStringExists(): void
    {
        $property = new PropertyReflection(
            'name',
            ['DataMapper\Tests\MockClasses\ItemConstructor'],
            $this->annotationEmpty(),
        );

        $this->assertSame('DataMapper\Tests\MockClasses\ItemConstructor', $property->getClassString());

        $property = new PropertyReflection(
            'name',
            ['DataMapper\Tests\MockClasses\ItemConstructor[]'],
            $this->annotationEmpty(),
        );

        $this->assertSame('DataMapper\Tests\MockClasses\ItemConstructor', $property->getClassString());

        $property = new PropertyReflection(
            'name',
            ['string'],
            $this->annotationComplex(),
        );

        $this->assertSame('DataMapper\Tests\MockClasses\ItemConstructor', $property->getClassString());
    }

    public function testIsOneTypeFalse(): void
    {
        $property = new PropertyReflection(
            'name',
            [],
            $this->annotationEmpty(),
        );

        $this->assertFalse($property->isOneType());

        $property = new PropertyReflection(
            'name',
            ['string', 'bool'],
            $this->annotationEmpty(),
        );

        $this->assertFalse($property->isOneType());

        $property = new PropertyReflection(
            'name',
            ['string'],
            $this->annotationComplex(),
        );

        $this->assertFalse($property->isOneType());
    }

    public function testIsOneTypeTrue(): void
    {
        $property = new PropertyReflection(
            'name',
            ['string'],
            $this->annotationEmpty(),
        );

        $this->assertTrue($property->isOneType());

        $property = new PropertyReflection(
            'name',
            ['string'],
            $this->annotationSimple(),
        );

        $this->assertTrue($property->isOneType());

        $property = new PropertyReflection(
            'name',
            ['null', 'string'],
            $this->annotationSimple(),
        );

        $this->assertTrue($property->isOneType());
    }
}
