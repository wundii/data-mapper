<?php

declare(strict_types=1);

namespace Unit\Resolver;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use Wundii\DataMapper\Dto\AnnotationDto;
use Wundii\DataMapper\Dto\ParameterDto;
use Wundii\DataMapper\Enum\DataTypeEnum;
use Wundii\DataMapper\Resolver\ReflectionElementResolver;

class ReflectionElementResolverTest extends TestCase
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

    public function testTargetTypesEmpty(): void
    {
        $targetTypes = (new ReflectionElementResolver())->getTargetTypes(
            'name',
            ['string'],
            $this->annotationEmpty(),
        );

        $this->assertSame(['string'], $targetTypes);
    }

    public function testTargetTypesSimple(): void
    {
        $targetTypes = (new ReflectionElementResolver())->getTargetTypes(
            'name',
            ['string'],
            $this->annotationSimple(),
        );

        $this->assertSame(['string'], $targetTypes);
    }

    public function testTargetTypesComplex(): void
    {
        $targetTypes = (new ReflectionElementResolver())->getTargetTypes(
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

    public function testTargetTypesArraySetter(): void
    {
        $targetTypes = (new ReflectionElementResolver())->getTargetTypes(
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

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testTypesEmpty(): void
    {
        $ReflectionElementParser = new ReflectionElementResolver();

        $reflector = $this->createMock(ReflectionIntersectionType::class);

        $this->assertSame([], $ReflectionElementParser->types(null));
        $this->assertSame([], $ReflectionElementParser->types($reflector));
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testTypesReflectionNamedType(): void
    {
        $ReflectionElementParser = new ReflectionElementResolver();

        $reflector = $this->createMock(ReflectionNamedType::class);
        $reflector->method('getName')->willReturn('string');
        $reflector->method('allowsNull')->willReturn(false);

        $this->assertSame(['string'], $ReflectionElementParser->types($reflector));

        $reflector = $this->createMock(ReflectionNamedType::class);
        $reflector->method('getName')->willReturn('string');
        $reflector->method('allowsNull')->willReturn(true);

        $this->assertSame(['string', 'null'], $ReflectionElementParser->types($reflector));

        $reflector = $this->createMock(ReflectionNamedType::class);
        $reflector->method('getName')->willReturn('null');
        $reflector->method('allowsNull')->willReturn(true);

        $this->assertSame(['null'], $ReflectionElementParser->types($reflector));
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testTypesReflectionUnionType(): void
    {
        $reflector01 = $this->createMock(ReflectionNamedType::class);
        $reflector01->method('getName')->willReturn('float');

        $reflector02 = $this->createMock(ReflectionNamedType::class);
        $reflector02->method('getName')->willReturn('string');

        $reflector03 = $this->createMock(ReflectionNamedType::class);
        $reflector03->method('getName')->willReturn('null');

        $types = [
            $reflector01,
            $reflector02,
            $reflector03,
        ];

        $ReflectionElementParser = new ReflectionElementResolver();

        $reflector = $this->createMock(ReflectionUnionType::class);
        $reflector->method('getTypes')->willReturn($types);

        $this->assertSame(['float', 'string', 'null'], $ReflectionElementParser->types($reflector));
    }

    /**
     * @throws Exception
     */
    public function getMockMethod(string $accessible = 'isPublic'): ReflectionMethod
    {
        $mockMethod = $this->createMock(ReflectionMethod::class);
        $mockMethod->method($accessible)->willReturn(true);
        return $mockMethod;
    }

    /**
     * @param string[] $types
     * @throws Exception
     */
    public function getMockType(array $types): null|ReflectionType
    {
        if (count($types) === 0) {
            return null;
        }

        if (count($types) === 1) {
            $mockType = $this->createMock(ReflectionNamedType::class);
            $mockType->method('getName')->willReturn($types[0]);
            $mockType->method('allowsNull')->willReturn($types[0] === 'null');
            return $mockType;
        }

        $mockTypes = [];
        foreach ($types as $type) {
            $mockType = $this->createMock(ReflectionNamedType::class);
            $mockType->method('getName')->willReturn($type);
            $mockType->method('allowsNull')->willReturn($type === 'null');
            $mockTypes[] = $mockType;
        }

        $mockUnionType = $this->createMock(ReflectionUnionType::class);
        $mockUnionType->method('getTypes')->willReturn($mockTypes);
        return $mockUnionType;
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function testTargetTypeNull(): void
    {
        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['string']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationSimple(),
        );

        $this->assertNull($property->getTargetType());

        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('findMeIfYouCan');
        $mockParameter->method('getType')->willReturn($this->getMockType(['string']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationParameter(),
        );

        $this->assertNull($property->getTargetType());
    }

    /**
     * @throws Exception
     * @throws ReflectionException
     */
    public function testTargetTypeExists(): void
    {
        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['MockClasses\ItemConstructor']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationEmpty(),
        );

        $this->assertSame('MockClasses\ItemConstructor', $property->getTargetType());

        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['DateTimeInterface']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationEmpty(),
        );

        $this->assertSame('DateTimeInterface', $property->getTargetType());

        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['MockClasses\ItemConstructor[]']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationEmpty(),
        );

        $this->assertSame('MockClasses\ItemConstructor', $property->getTargetType());

        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['DateTimeInterface[]']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationEmpty(),
        );

        $this->assertSame('DateTimeInterface', $property->getTargetType());

        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['string']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationComplex(),
        );

        $this->assertSame('MockClasses\ItemConstructor', $property->getTargetType());

        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['array', 'string[]']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationEmpty(),
        );

        $this->assertSame('string', $property->getTargetType());

        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['self', 'null']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            'MockClasses\ItemConstructor',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationEmpty(),
        );

        $this->assertSame('MockClasses\ItemConstructor', $property->getTargetType());
    }

    /**
     * @throws Exception
     * @throws ReflectionException
     */
    public function testGetTypeNull(): void
    {
        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType([]));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationEmpty(),
        );

        $this->assertSame(DataTypeEnum::NULL, $property->getDataType());

        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['string', 'bool']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationEmpty(),
        );

        $this->assertSame(DataTypeEnum::NULL, $property->getDataType());

        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['array', 'MockClasses\ItemConstructor']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationEmpty(),
        );

        $this->assertSame(DataTypeEnum::NULL, $property->getDataType());
    }

    /**
     * @throws Exception
     * @throws ReflectionException
     */
    public function testIsNullable(): void
    {
        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['string']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationEmpty(),
        );

        $this->assertFalse($property->isNullable());

        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['string', 'null']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationEmpty(),
        );

        $this->assertTrue($property->isNullable());

        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['NULL', 'string']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationEmpty(),
        );

        $this->assertTrue($property->isNullable());
    }

    /**
     * @throws Exception
     * @throws ReflectionException
     */
    public function testGetType(): void
    {
        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['string']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationEmpty(),
        );

        $this->assertSame(DataTypeEnum::STRING, $property->getDataType());

        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['array', 'MockClasses\ItemConstructor[]']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationEmpty(),
        );

        $this->assertSame(DataTypeEnum::ARRAY, $property->getDataType());

        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['string']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationSimple(),
        );

        $this->assertSame(DataTypeEnum::STRING, $property->getDataType());

        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['null', 'string']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationSimple(),
        );

        $this->assertSame(DataTypeEnum::STRING, $property->getDataType());

        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['null', 'MockClasses\ItemConstructor']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            '',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationEmpty(),
        );

        $this->assertSame(DataTypeEnum::OBJECT, $property->getDataType());

        $mockParameter = $this->createMock(ReflectionParameter::class);
        $mockParameter->method('getName')->willReturn('name');
        $mockParameter->method('getType')->willReturn($this->getMockType(['self', 'null']));
        $mockParameter->method('isDefaultValueAvailable')->willReturn(false);

        $property = (new ReflectionElementResolver())->resolve(
            'MockClasses\ItemConstructor',
            $this->getMockMethod(),
            $mockParameter,
            $this->annotationEmpty(),
        );

        $this->assertSame(DataTypeEnum::OBJECT, $property->getDataType());
    }
}
