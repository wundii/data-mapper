<?php

declare(strict_types=1);

namespace Unit\Parser;

use Exception;
use MockClasses\GroupUseStatements;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Wundii\DataMapper\Dto\UseStatementDto;
use Wundii\DataMapper\Dto\UseStatementsDto;
use Wundii\DataMapper\Parser\ReflectionUseParser;

class ReflectionUseParserTest extends TestCase
{
    public function testBasename(): void
    {
        $reflectionTokenResolver = new ReflectionUseParser();

        $basename = $reflectionTokenResolver->basename('MockClasses\RootConstructor');

        $this->assertEquals('RootConstructor', $basename);

        $basename = $reflectionTokenResolver->basename('DataMapper/Tests/MockClasses/RootConstructor');

        $this->assertEquals('RootConstructor', $basename);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws Exception
     */
    public function testParseToken(): void
    {
        $reflectionClass = $this->createMock(ReflectionClass::class);
        $reflectionClass->method('getName')
            ->willReturn('MockClasses\RootConstructor');
        $reflectionClass->method('getFileName')
            ->willReturn(__DIR__ . '/../../MockClasses/RootConstructor.php');
        $reflectionClass->method('getNamespaceName')
            ->willReturn('');

        $reflectionTokenResolver = new ReflectionUseParser();

        $expected = new UseStatementsDto(
            null,
            [
                new UseStatementDto('MockClasses\RootConstructor', 'RootConstructor'),
                new UseStatementDto('MockClasses\Sub\SubItemConstructor', 'SubItemConstructor'),
            ],
        );

        $this->assertEquals($expected, $reflectionTokenResolver->parseToken($reflectionClass));

        $reflectionClass = $this->createMock(ReflectionClass::class);
        $reflectionClass->method('getName')
            ->willReturn('MockClasses\RootConstructor');
        $reflectionClass->method('getFileName')
            ->willReturn(__DIR__ . '/../../MockClasses/RootConstructor.php');
        $reflectionClass->method('getNamespaceName')
            ->willReturn('MockClasses');

        $reflectionTokenResolver = new ReflectionUseParser();

        $expected = new UseStatementsDto(
            'MockClasses',
            [
                new UseStatementDto('MockClasses\RootConstructor', 'RootConstructor'),
                new UseStatementDto('MockClasses\Sub\SubItemConstructor', 'SubItemConstructor'),
            ],
        );

        $this->assertEquals($expected, $reflectionTokenResolver->parseToken($reflectionClass));
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws Exception
     */
    public function testParseTokenFileNotFound(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('File not found: invalid-file-name');

        $reflectionClass = $this->createMock(ReflectionClass::class);
        $reflectionClass->method('getName')
            ->willReturn('MockClasses\RootConstructor');
        $reflectionClass->method('getFileName')
            ->willReturn('invalid-file-name');

        $reflectionTokenResolver = new ReflectionUseParser();
        $reflectionTokenResolver->parseToken($reflectionClass);
    }

    /**
     * @throws Exception
     */
    public function testResolveInternalFunction(): void
    {
        $reflectionTokenResolver = new ReflectionUseParser();
        $useStatementsReflection = $reflectionTokenResolver->parse('DateTime');

        $this->assertNull($useStatementsReflection);
    }

    /**
     * @throws Exception
     */
    public function testParseTokenGroupUseStatements(): void
    {
        $reflectionTokenResolver = new ReflectionUseParser();
        $useStatementsDto = $reflectionTokenResolver->parse(GroupUseStatements::class);

        $this->assertInstanceOf(UseStatementsDto::class, $useStatementsDto);

        $classStrings = array_map(
            static fn (UseStatementDto $useStatementDto): string => $useStatementDto->getClass(),
            $useStatementsDto->getUseStatements(),
        );

        $this->assertContains('Wundii\DataMapper\Dto\AnnotationDto', $classStrings);
        $this->assertContains('Wundii\DataMapper\Dto\ParameterDto', $classStrings);
        $this->assertContains('MockClasses\Sub\SubItemConstructor', $classStrings);

        $this->assertNotContains('Wundii\DataMapper\Dto\AnnotationDtoParameterDto', $classStrings);
        $this->assertNotContains('array_map', $classStrings);
        $this->assertNotContains('PHP_EOL', $classStrings);
    }

    /**
     * @throws Exception
     */
    public function testParseTokenGroupUseStatementsAliases(): void
    {
        $reflectionTokenResolver = new ReflectionUseParser();
        $useStatementsDto = $reflectionTokenResolver->parse(GroupUseStatements::class);

        $this->assertInstanceOf(UseStatementsDto::class, $useStatementsDto);

        $this->assertSame('Wundii\DataMapper\Dto\AnnotationDto', $useStatementsDto->findClassString('AnnotationDto'));
        $this->assertSame('Wundii\DataMapper\Dto\ParameterDto', $useStatementsDto->findClassString('ParameterDto'));
    }

    /**
     * @throws Exception
     */
    public function testParseUsesCache(): void
    {
        $reflectionTokenResolver = new ReflectionUseParser();

        $first = $reflectionTokenResolver->parse(GroupUseStatements::class);
        $second = $reflectionTokenResolver->parse(GroupUseStatements::class);

        $this->assertSame($first, $second);

        ReflectionUseParser::clearUseStatementsCache();

        $third = $reflectionTokenResolver->parse(GroupUseStatements::class);

        $this->assertNotSame($first, $third);
        $this->assertEquals($first, $third);
    }
}
