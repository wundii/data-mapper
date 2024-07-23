<?php

declare(strict_types=1);

namespace DataMapper\Tests\Unit\Resolver;

use DataMapper\Reflection\AnnotationReflection;
use DataMapper\Reflection\ParameterReflection;
use DataMapper\Reflection\UseStatementReflection;
use DataMapper\Reflection\UseStatementsReflection;
use DataMapper\Resolver\ReflectionObjectResolver;
use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;

class ReflectionObjectResolverTest extends TestCase
{
    public function testCompleteClassStrings(): void
    {
        $useStatementsReflection = new UseStatementsReflection([
            new UseStatementReflection(
                'DataMapper\Tests\MockClasses\ItemConstructor',
                'ItemConstructor',
            ),
            new UseStatementReflection(
                'DataMapper\Tests\MockClasses\RootConstructor',
                'RootConstructor',
            ),
        ]);

        $types = [
            'float',
            'RootConstructor',
            'ItemConstructor[]',
            'string[]',
            'bool',
        ];
        $expected = [
            'float',
            'DataMapper\Tests\MockClasses\RootConstructor',
            'DataMapper\Tests\MockClasses\ItemConstructor[]',
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
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection([], []);

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationWrongStartWith(): void
    {
        $annotation = '@param string $name';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection([], []);

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationParamBool(): void
    {
        $annotation = '/** @param bool $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('name', ['bool']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationParamInt(): void
    {
        $annotation = '/** @param int $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('name', ['int']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationParamFloat(): void
    {
        $annotation = '/** @param float $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('name', ['float']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationParamString(): void
    {
        $annotation = '/** @param string $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('name', ['string']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationParamArray(): void
    {
        $annotation = '/** @param array $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('name', ['array']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationParamObject(): void
    {
        $annotation = '/** @param ItemConstructor $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('name', ['ItemConstructor']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $annotation = '/** @param ItemConstructor[] $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('name', ['ItemConstructor[]']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([
            new UseStatementReflection(
                'DataMapper\Tests\MockClasses\ItemConstructor',
                'ItemConstructor',
            ),
        ]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('name', ['DataMapper\Tests\MockClasses\ItemConstructor[]']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationParamStringWithNull(): void
    {
        $annotation = '/** @param ?string $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('name', ['null', 'string']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $annotation = '/** @param null|string $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('name', ['null', 'string']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationParamMultiTypes(): void
    {
        $annotation = '/** @param null|int|string $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('name', ['null', 'int', 'string']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
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
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('name', ['null', 'int', 'string']),
                new ParameterReflection('price', ['float']),
                new ParameterReflection('options', ['string[]']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationVarBool(): void
    {
        $annotation = '/** @var bool */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [],
            ['bool'],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationVarInt(): void
    {
        $annotation = '/** @var int */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [],
            ['int'],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationVarFloat(): void
    {
        $annotation = '/** @var float */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [],
            ['float'],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationVarString(): void
    {
        $annotation = '/** @var string */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [],
            ['string'],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationVarArray(): void
    {
        $annotation = '/** @var array */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [],
            ['array'],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationVarObject(): void
    {
        $annotation = '/** @var DataMapper\Tests\MockClasses\ItemConstructor */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [],
            ['DataMapper\Tests\MockClasses\ItemConstructor'],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $annotation = '/** @var ItemConstructor[] */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [],
            ['ItemConstructor[]'],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([
            new UseStatementReflection(
                'DataMapper\Tests\MockClasses\ItemConstructor',
                'ItemConstructor',
            ),
        ]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [],
            ['DataMapper\Tests\MockClasses\ItemConstructor[]'],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseAnnotationVarStringWithNull(): void
    {
        $annotation = '/** @var ?string */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [],
            ['null', 'string'],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $annotation = '/** @var null|string */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [],
            ['null', 'string'],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
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
        $useStatementsReflection = new UseStatementsReflection([]);
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($useStatementsReflection, $annotation);

        $expected = new AnnotationReflection(
            [],
            ['null', 'int', 'string']
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
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
