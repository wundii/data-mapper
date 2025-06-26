<?php

declare(strict_types=1);

namespace Unit\Resolver;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Dto\AnnotationDto;
use Wundii\DataMapper\Dto\ParameterDto;
use Wundii\DataMapper\Dto\UseStatementDto;
use Wundii\DataMapper\Dto\UseStatementsDto;
use Wundii\DataMapper\Resolver\ReflectionAnnotationResolver;

class ReflectionAnnotationResolverTest extends TestCase
{
    public function testCompleteClassStringsWithoutUseStatements(): void
    {
        $types = [
            'float',
            'RootConstructor',
            'ItemConstructor[]',
            'string[]',
            'bool',
        ];

        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);

        $this->assertSame($types, $reflectionAnnotationResolver->completeClassStrings($types));
    }

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

        $reflectionAnnotationResolver = new ReflectionAnnotationResolver($useStatementsReflection);

        $this->assertSame($expected, $reflectionAnnotationResolver->completeClassStrings($types));
    }

    public function testParseAnnotationEmpty(): void
    {
        $annotation = '';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $this->assertNull($annotationReflection);
    }

    public function testParseAnnotationWrongStartWith(): void
    {
        $annotation = '@param string $name';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $this->assertNull($annotationReflection);
    }

    public function testParseAnnotationParamBool(): void
    {
        $annotation = '/** @param bool $name */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['bool']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    public function testParseAnnotationParamInt(): void
    {
        $annotation = '/** @param int $name */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['int']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    public function testParseAnnotationParamFloat(): void
    {
        $annotation = '/** @param float $name */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['float']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    public function testParseAnnotationParamString(): void
    {
        $annotation = '/** @param string $name */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['string']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    public function testParseAnnotationParamArray(): void
    {
        $annotation = '/** @param array $name */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['array']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    public function testParseAnnotationParamObject(): void
    {
        $annotation = '/** @param ItemConstructor $name */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['ItemConstructor']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $annotation = '/** @param ItemConstructor[] $name */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['ItemConstructor[]']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $useStatementsReflection = new UseStatementsDto(
            null,
            [
                new UseStatementDto(
                    'MockClasses\ItemConstructor',
                    'ItemConstructor',
                ),
            ]
        );
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver($useStatementsReflection);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['MockClasses\ItemConstructor[]']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    public function testParseAnnotationParamStringWithNull(): void
    {
        $annotation = '/** @param ?string $name */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['null', 'string']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $annotation = '/** @param null|string $name */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['null', 'string']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    public function testParseAnnotationParamMultiTypes(): void
    {
        $annotation = '/** @param null|int|string $name */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [
                new ParameterDto('name', ['null', 'int', 'string']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    public function testParseAnnotationParamMultiParameter(): void
    {
        $annotation = <<<TEXT
/**
 * @param null|int|string \$name
 * @param float \$price
 * @param string[] \$options
 */
TEXT;

        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

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

    public function testParseAnnotationVarBool(): void
    {
        $annotation = '/** @var bool */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [],
            ['bool'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    public function testParseAnnotationVarInt(): void
    {
        $annotation = '/** @var int */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [],
            ['int'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    public function testParseAnnotationVarFloat(): void
    {
        $annotation = '/** @var float */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [],
            ['float'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    public function testParseAnnotationVarString(): void
    {
        $annotation = '/** @var string */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [],
            ['string'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    public function testParseAnnotationVarArray(): void
    {
        $annotation = '/** @var array */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [],
            ['array'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    public function testParseAnnotationVarObject(): void
    {
        $annotation = '/** @var MockClasses\ItemConstructor */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [],
            ['MockClasses\ItemConstructor'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $annotation = '/** @var ItemConstructor[] */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [],
            ['ItemConstructor[]'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $useStatementsReflection = new UseStatementsDto(
            null,
            [
                new UseStatementDto(
                    'MockClasses\ItemConstructor',
                    'ItemConstructor',
                ),
            ]
        );
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver($useStatementsReflection);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [],
            ['MockClasses\ItemConstructor[]'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    public function testParseAnnotationVarStringWithNull(): void
    {
        $annotation = '/** @var ?string */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [],
            ['null', 'string'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $annotation = '/** @var null|string */';
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [],
            ['null', 'string'],
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }

    public function testParseAnnotationVarMultiTypes(): void
    {
        $annotation = <<<TEXT
/** 
 * @var float 
 * @var null|int|string
 */
TEXT;
        $reflectionAnnotationResolver = new ReflectionAnnotationResolver(null);
        $annotationReflection = $reflectionAnnotationResolver->resolve($annotation);

        $expected = new AnnotationDto(
            [],
            ['null', 'int', 'string']
        );

        $this->assertInstanceOf(AnnotationDto::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }
}
