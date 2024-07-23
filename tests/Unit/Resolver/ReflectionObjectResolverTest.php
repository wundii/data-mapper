<?php

declare(strict_types=1);

namespace DataMapper\Tests\Unit\Resolver;

use DataMapper\Reflection\AnnotationReflection;
use DataMapper\Reflection\ParameterReflection;
use DataMapper\Resolver\ReflectionObjectResolver;
use Exception;
use PHPUnit\Framework\TestCase;

class ReflectionObjectResolverTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testParseAnnotationEmpty(): void
    {
        $annotation = '';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

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
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

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
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('$name', ['bool']),
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
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('$name', ['int']),
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
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('$name', ['float']),
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
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('$name', ['string']),
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
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('$name', ['array']),
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
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('$name', ['ItemConstructor']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $annotation = '/** @param ItemConstructor[] $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('$name', ['ItemConstructor[]']),
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
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('$name', ['null', 'string']),
            ],
            [],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $annotation = '/** @param null|string $name */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('$name', ['null', 'string']),
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
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('$name', ['null', 'int', 'string']),
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
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

        $expected = new AnnotationReflection(
            [
                new ParameterReflection('$name', ['null', 'int', 'string']),
                new ParameterReflection('$price', ['float']),
                new ParameterReflection('$options', ['string[]']),
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
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

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
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

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
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

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
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

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
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

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
        $annotation = '/** @var ItemConstructor */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

        $expected = new AnnotationReflection(
            [],
            ['ItemConstructor'],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $annotation = '/** @var ItemConstructor[] */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

        $expected = new AnnotationReflection(
            [],
            ['ItemConstructor[]'],
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
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

        $expected = new AnnotationReflection(
            [],
            ['null', 'string'],
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);

        $annotation = '/** @var null|string */';
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

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
        $annotationReflection = $reflectionObjectResolver->parseAnnotation($annotation);

        $expected = new AnnotationReflection(
            [],
            ['null', 'int', 'string']
        );

        $this->assertInstanceOf(AnnotationReflection::class, $annotationReflection);
        $this->assertEquals($expected, $annotationReflection);
    }
}
