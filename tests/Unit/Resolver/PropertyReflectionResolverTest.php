<?php

declare(strict_types=1);

namespace Unit\Resolver;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Enum\DataTypeEnum;
use Wundii\DataMapper\Enum\VisibilityEnum;
use Wundii\DataMapper\Reflection\AnnotationReflection;
use Wundii\DataMapper\Reflection\ParameterReflection;
use Wundii\DataMapper\Resolver\PropertyReflectionResolver;

class PropertyReflectionResolverTest extends TestCase
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

    public function annotationArraySetter(): AnnotationReflection
    {
        return new AnnotationReflection(
            [new ParameterReflection('myStrings', ['string'])],
            [],
        );
    }

    public function annotationComplex(): AnnotationReflection
    {
        return new AnnotationReflection(
            [
                new ParameterReflection('name', ['string', 'null', 'float']),
                new ParameterReflection('data', ['array']),
                new ParameterReflection('item', ['MockClasses\ItemConstructor']),
            ],
            ['bool', 'string', 'MockClasses\ItemConstructor[]', 'MockClasses\RootConstructor'],
        );
    }

    public function annotationParameter(): AnnotationReflection
    {
        return new AnnotationReflection(
            [
                new ParameterReflection('name', ['string', 'null', 'float']),
                new ParameterReflection('data', ['array']),
                new ParameterReflection('item', ['MockClasses\ItemConstructor']),
            ],
            [],
        );
    }

    public function annotationSelf(): AnnotationReflection
    {
        return new AnnotationReflection(
            [new ParameterReflection('name', ['string'])],
            ['string'],
        );
    }

    public function testTypesEmpty(): void
    {
        $targetTypes = (new PropertyReflectionResolver())->getTargetTypes(
            'name',
            ['string'],
            $this->annotationEmpty(),
        );

        $this->assertSame(['string'], $targetTypes);
    }

    public function testTypesSimple(): void
    {
        $targetTypes = (new PropertyReflectionResolver())->getTargetTypes(
            'name',
            ['string'],
            $this->annotationSimple(),
        );

        $this->assertSame(['string'], $targetTypes);
    }

    public function testTypesComplex(): void
    {
        $targetTypes = (new PropertyReflectionResolver())->getTargetTypes(
            'name',
            ['string'],
            $this->annotationComplex(),
        );

        $expected = [
            'string',
            'bool',
            'MockClasses\ItemConstructor[]',
            'MockClasses\RootConstructor',
            'null',
            'float',
        ];

        $this->assertSame($expected, $targetTypes);
    }

    public function testTypesArraySetter(): void
    {
        $targetTypes = (new PropertyReflectionResolver())->getTargetTypes(
            'setMyStrings',
            ['array'],
            $this->annotationArraySetter(),
        );

        $expected = [
            'array',
            'string',
        ];

        $this->assertSame($expected, $targetTypes);
    }

    public function testTargetTypeNull(): void
    {
        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['string'],
            $this->annotationSimple(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertNull($property->getTargetType());

        $property = (new PropertyReflectionResolver())->resolve(
            'findMeIfYouCan',
            ['string'],
            $this->annotationParameter(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertNull($property->getTargetType());
    }

    public function testTargetTypeExists(): void
    {
        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['MockClasses\ItemConstructor'],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertSame('MockClasses\ItemConstructor', $property->getTargetType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['DateTimeInterface'],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertSame('DateTimeInterface', $property->getTargetType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['MockClasses\ItemConstructor[]'],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertSame('MockClasses\ItemConstructor', $property->getTargetType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['DateTimeInterface[]'],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertSame('DateTimeInterface', $property->getTargetType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['string'],
            $this->annotationComplex(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertSame('MockClasses\ItemConstructor', $property->getTargetType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['array', 'string[]'],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertSame('string', $property->getTargetType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['self', 'null'],
            $this->annotationEmpty(),
            'MockClasses\ItemConstructor',
            VisibilityEnum::PRIVATE,
        );

        $this->assertSame('MockClasses\ItemConstructor', $property->getTargetType());
    }

    public function testIsOneTypeFalse(): void
    {
        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            [],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertFalse($property->isOneType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['string', 'bool'],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertFalse($property->isOneType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['string'],
            $this->annotationComplex(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertFalse($property->isOneType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['array', 'MockClasses\ItemConstructor'],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertFalse($property->isOneType());
    }

    public function testIsOneTypeTrue(): void
    {
        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['string'],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertTrue($property->isOneType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['array', 'MockClasses\ItemConstructor[]'],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertTrue($property->isOneType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['string'],
            $this->annotationSimple(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertTrue($property->isOneType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['null', 'string'],
            $this->annotationSimple(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertTrue($property->isOneType());
    }

    public function testGetTypeNull(): void
    {
        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            [],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertSame(DataTypeEnum::NULL, $property->getDataType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['string', 'bool'],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertSame(DataTypeEnum::NULL, $property->getDataType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['array', 'MockClasses\ItemConstructor'],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertSame(DataTypeEnum::NULL, $property->getDataType());
    }

    public function testIsNullable(): void
    {
        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['string'],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertFalse($property->isNullable());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['string', 'null'],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertTrue($property->isNullable());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['NULL', 'string'],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertTrue($property->isNullable());
    }

    public function testGetType(): void
    {
        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['string'],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertSame(DataTypeEnum::STRING, $property->getDataType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['array', 'MockClasses\ItemConstructor[]'],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertSame(DataTypeEnum::ARRAY, $property->getDataType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['string'],
            $this->annotationSimple(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertSame(DataTypeEnum::STRING, $property->getDataType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['null', 'string'],
            $this->annotationSimple(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertSame(DataTypeEnum::STRING, $property->getDataType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['null', 'MockClasses\ItemConstructor'],
            $this->annotationEmpty(),
            '',
            VisibilityEnum::PRIVATE,
        );

        $this->assertSame(DataTypeEnum::OBJECT, $property->getDataType());

        $property = (new PropertyReflectionResolver())->resolve(
            'name',
            ['self', 'null'],
            $this->annotationEmpty(),
            'MockClasses\ItemConstructor',
            VisibilityEnum::PRIVATE,
        );

        $this->assertSame(DataTypeEnum::OBJECT, $property->getDataType());
    }
}
