<?php

declare(strict_types=1);

namespace Unit\Resolver;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Dto\AnnotationDto;
use Wundii\DataMapper\Dto\ParameterDto;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\DataTypeEnum;
use Wundii\DataMapper\Resolver\PropertyDtoResolver;

class PropertyReflectionResolverTest extends TestCase
{
    public function annotationEmpty(): AnnotationDto
    {
        return new AnnotationDto([], []);
    }

    public function annotationSimple(): AnnotationDto
    {
        return new AnnotationDto(
            [new ParameterDto('name', ['string'])],
            ['string'],
        );
    }

    public function annotationArraySetter(): AnnotationDto
    {
        return new AnnotationDto(
            [new ParameterDto('myStrings', ['string'])],
            [],
        );
    }

    public function annotationComplex(): AnnotationDto
    {
        return new AnnotationDto(
            [
                new ParameterDto('name', ['string', 'null', 'float']),
                new ParameterDto('data', ['array']),
                new ParameterDto('item', ['MockClasses\ItemConstructor']),
            ],
            ['bool', 'string', 'MockClasses\ItemConstructor[]', 'MockClasses\RootConstructor'],
        );
    }

    public function annotationParameter(): AnnotationDto
    {
        return new AnnotationDto(
            [
                new ParameterDto('name', ['string', 'null', 'float']),
                new ParameterDto('data', ['array']),
                new ParameterDto('item', ['MockClasses\ItemConstructor']),
            ],
            [],
        );
    }

    public function annotationSelf(): AnnotationDto
    {
        return new AnnotationDto(
            [new ParameterDto('name', ['string'])],
            ['string'],
        );
    }

    public function testTypesEmpty(): void
    {
        $targetTypes = (new PropertyDtoResolver())->getTargetTypes(
            'name',
            ['string'],
            $this->annotationEmpty(),
        );

        $this->assertSame(['string'], $targetTypes);
    }

    public function testTypesSimple(): void
    {
        $targetTypes = (new PropertyDtoResolver())->getTargetTypes(
            'name',
            ['string'],
            $this->annotationSimple(),
        );

        $this->assertSame(['string'], $targetTypes);
    }

    public function testTypesComplex(): void
    {
        $targetTypes = (new PropertyDtoResolver())->getTargetTypes(
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
        $targetTypes = (new PropertyDtoResolver())->getTargetTypes(
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
        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['string'],
            $this->annotationSimple(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertNull($property->getTargetType());

        $property = (new PropertyDtoResolver())->resolve(
            'findMeIfYouCan',
            ['string'],
            $this->annotationParameter(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertNull($property->getTargetType());
    }

    public function testTargetTypeExists(): void
    {
        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['MockClasses\ItemConstructor'],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertSame('MockClasses\ItemConstructor', $property->getTargetType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['DateTimeInterface'],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertSame('DateTimeInterface', $property->getTargetType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['MockClasses\ItemConstructor[]'],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertSame('MockClasses\ItemConstructor', $property->getTargetType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['DateTimeInterface[]'],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertSame('DateTimeInterface', $property->getTargetType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['string'],
            $this->annotationComplex(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertSame('MockClasses\ItemConstructor', $property->getTargetType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['array', 'string[]'],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertSame('string', $property->getTargetType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['self', 'null'],
            $this->annotationEmpty(),
            'MockClasses\ItemConstructor',
            AccessibleEnum::PRIVATE,
        );

        $this->assertSame('MockClasses\ItemConstructor', $property->getTargetType());
    }

    public function testIsOneTypeFalse(): void
    {
        $property = (new PropertyDtoResolver())->resolve(
            'name',
            [],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertFalse($property->isOneType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['string', 'bool'],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertFalse($property->isOneType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['string'],
            $this->annotationComplex(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertFalse($property->isOneType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['array', 'MockClasses\ItemConstructor'],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertFalse($property->isOneType());
    }

    public function testIsOneTypeTrue(): void
    {
        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['string'],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertTrue($property->isOneType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['array', 'MockClasses\ItemConstructor[]'],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertTrue($property->isOneType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['string'],
            $this->annotationSimple(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertTrue($property->isOneType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['null', 'string'],
            $this->annotationSimple(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertTrue($property->isOneType());
    }

    public function testGetTypeNull(): void
    {
        $property = (new PropertyDtoResolver())->resolve(
            'name',
            [],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertSame(DataTypeEnum::NULL, $property->getDataType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['string', 'bool'],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertSame(DataTypeEnum::NULL, $property->getDataType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['array', 'MockClasses\ItemConstructor'],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertSame(DataTypeEnum::NULL, $property->getDataType());
    }

    public function testIsNullable(): void
    {
        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['string'],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertFalse($property->isNullable());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['string', 'null'],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertTrue($property->isNullable());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['NULL', 'string'],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertTrue($property->isNullable());
    }

    public function testGetType(): void
    {
        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['string'],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertSame(DataTypeEnum::STRING, $property->getDataType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['array', 'MockClasses\ItemConstructor[]'],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertSame(DataTypeEnum::ARRAY, $property->getDataType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['string'],
            $this->annotationSimple(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertSame(DataTypeEnum::STRING, $property->getDataType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['null', 'string'],
            $this->annotationSimple(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertSame(DataTypeEnum::STRING, $property->getDataType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['null', 'MockClasses\ItemConstructor'],
            $this->annotationEmpty(),
            '',
            AccessibleEnum::PRIVATE,
        );

        $this->assertSame(DataTypeEnum::OBJECT, $property->getDataType());

        $property = (new PropertyDtoResolver())->resolve(
            'name',
            ['self', 'null'],
            $this->annotationEmpty(),
            'MockClasses\ItemConstructor',
            AccessibleEnum::PRIVATE,
        );

        $this->assertSame(DataTypeEnum::OBJECT, $property->getDataType());
    }
}
