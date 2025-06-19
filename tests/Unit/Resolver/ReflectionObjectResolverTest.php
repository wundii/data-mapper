<?php

declare(strict_types=1);

namespace Unit\Resolver;

use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;
use Wundii\DataMapper\Dto\AnnotationDto;
use Wundii\DataMapper\Dto\ParameterDto;
use Wundii\DataMapper\Dto\UseStatementDto;
use Wundii\DataMapper\Dto\UseStatementsDto;
use Wundii\DataMapper\Resolver\ReflectionObjectResolver;

class ReflectionObjectResolverTest extends TestCase
{
    public function testCompleteClassStrings(): void
    {
        $useStatementsReflection = new UseStatementsDto(
            null,
            [
                new UseStatementDto(
                    'MockClasses\ItemConstructor',
                    'ItemConstructor',
                ),
                new UseStatementDto(
                    'MockClasses\RootConstructor',
                    'RootConstructor',
                ),
            ],
        );

        $types = [
            'float',
            'RootConstructor',
            'ItemConstructor[]',
            'string[]',
            'bool',
        ];
        $expected = [
            'float',
            'MockClasses\RootConstructor',
            'MockClasses\ItemConstructor[]',
            'string[]',
            'bool',
        ];

        $reflectionObjectResolver = new ReflectionObjectResolver();

        $this->assertSame($expected, $reflectionObjectResolver->completeClassStrings($useStatementsReflection, $types));
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testName(): void
    {
        $expected = 'helloWorld';
        $reflectionObjectResolver = new ReflectionObjectResolver();

        $reflector = $this->createMock(ReflectionProperty::class);
        $reflector->method('getName')->willReturn($expected);

        $this->assertSame($expected, $reflectionObjectResolver->name($reflector));

        $reflector = $this->createMock(ReflectionParameter::class);
        $reflector->method('getName')->willReturn($expected);

        $this->assertSame($expected, $reflectionObjectResolver->name($reflector));

        $reflector = $this->createMock(ReflectionMethod::class);
        $reflector->method('getName')->willReturn($expected);

        $this->assertSame($expected, $reflectionObjectResolver->name($reflector));
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationEmpty(): void
    {
        $annotation = '';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto([], []);

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationWrongStartWith(): void
    {
        $annotation = '@param string $name';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto([], []);

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationParamBool(): void
    {
        $annotation = '/** @param bool $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['bool']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationParamInt(): void
    {
        $annotation = '/** @param int $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['int']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationParamFloat(): void
    {
        $annotation = '/** @param float $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['float']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationParamString(): void
    {
        $annotation = '/** @param string $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['string']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationParamArray(): void
    {
        $annotation = '/** @param array $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['array']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationParamObject(): void
    {
        $annotation = '/** @param ItemConstructor $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['ItemConstructor']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $annotation = '/** @param ItemConstructor[] $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['ItemConstructor[]']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(
            null,
            [
                new UseStatementDto(
                    'MockClasses\ItemConstructor',
                    'ItemConstructor',
                ),
            ]
        )
        ;
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['MockClasses\ItemConstructor[]']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationParamStringWithNull(): void
    {
        $annotation = '/** @param ?string $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['null', 'string']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $annotation = '/** @param null|string $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['null', 'string']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationParamMultiTypes(): void
    {
        $annotation = '/** @param null|int|string $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['null', 'int', 'string']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationParamMultiParameter(): void
    {
        $annotation = <<<TEXT
/**
 * @param null|int|string \$name
 * @param float \$price
 * @param string[] \$options
 */
TEXT;

        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['null', 'int', 'string']),
                new ParameterDto('price', ['float']),
                new ParameterDto('options', ['string[]']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationVarBool(): void
    {
        $annotation = '/** @var bool */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [],
            ['bool'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationVarInt(): void
    {
        $annotation = '/** @var int */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [],
            ['int'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationVarFloat(): void
    {
        $annotation = '/** @var float */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [],
            ['float'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationVarString(): void
    {
        $annotation = '/** @var string */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [],
            ['string'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationVarArray(): void
    {
        $annotation = '/** @var array */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [],
            ['array'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationVarObject(): void
    {
        $annotation = '/** @var MockClasses\ItemConstructor */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [],
            ['MockClasses\ItemConstructor'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $annotation = '/** @var ItemConstructor[] */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [],
            ['ItemConstructor[]'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(
            null,
            [
                new UseStatementDto(
                    'MockClasses\ItemConstructor',
                    'ItemConstructor',
                ),
            ]
        );
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [],
            ['MockClasses\ItemConstructor[]'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationVarStringWithNull(): void
    {
        $annotation = '/** @var ?string */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [],
            ['null', 'string'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $annotation = '/** @var null|string */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [],
            ['null', 'string'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationVarMultiTypes(): void
    {
        $annotation = <<<TEXT
/** 
 * @var float 
 * @var null|int|string
 */
TEXT;
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsDto(null, []);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationDto(
            [],
            ['null', 'int', 'string']
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testTypesEmpty(): void
    {
        $reflectionObjectResolver = new ReflectionObjectResolver();

        $reflector = $this->createMock(ReflectionIntersectionType::class);

        $this->assertSame([], $reflectionObjectResolver->types(null));
        $this->assertSame([], $reflectionObjectResolver->types($reflector));
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testTypesReflectionNamedType(): void
    {
        $reflectionObjectResolver = new ReflectionObjectResolver();

        $reflector = $this->createMock(ReflectionNamedType::class);
        $reflector->method('getName')->willReturn('string');
        $reflector->method('allowsNull')->willReturn(false);

        $this->assertSame(['string'], $reflectionObjectResolver->types($reflector));

        $reflector = $this->createMock(ReflectionNamedType::class);
        $reflector->method('getName')->willReturn('string');
        $reflector->method('allowsNull')->willReturn(true);

        $this->assertSame(['string', 'null'], $reflectionObjectResolver->types($reflector));

        $reflector = $this->createMock(ReflectionNamedType::class);
        $reflector->method('getName')->willReturn('null');
        $reflector->method('allowsNull')->willReturn(true);

        $this->assertSame(['null'], $reflectionObjectResolver->types($reflector));
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

        $reflectionObjectResolver = new ReflectionObjectResolver();

        $reflector = $this->createMock(ReflectionUnionType::class);
        $reflector->method('getTypes')->willReturn($types);

        $this->assertSame(['float', 'string', 'null'], $reflectionObjectResolver->types($reflector));
    }
}
