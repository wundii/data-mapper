<?php

declare(strict_types=1);

namespace Unit\Resolver;

use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Wundii\DataMapper\Dto\UseStatementDto;
use Wundii\DataMapper\Dto\UseStatementsDto;
use Wundii\DataMapper\Resolver\ReflectionUseResolver;

class ReflectionUseResolverTest extends TestCase
{
    public function testBasename(): void
    {
        $reflectionTokenResolver = new ReflectionUseResolver();

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
        $reflectionClass->method('getName')->willReturn('MockClasses\RootConstructor');
        $reflectionClass->method('getFileName')->willReturn(__DIR__ . '/../../MockClasses/RootConstructor.php');
        $reflectionClass->method('getNamespaceName')->willReturn('');

        $reflectionTokenResolver = new ReflectionUseResolver();

        $expected = new UseStatementsDto(
            null,
            [
                new UseStatementDto('MockClasses\RootConstructor', 'RootConstructor'),
                new UseStatementDto('MockClasses\Sub\SubItemConstructor', 'SubItemConstructor'),
            ],
        );

        $this->assertEquals($expected, $reflectionTokenResolver->parseToken($reflectionClass));

        $reflectionClass = $this->createMock(ReflectionClass::class);
        $reflectionClass->method('getName')->willReturn('MockClasses\RootConstructor');
        $reflectionClass->method('getFileName')->willReturn(__DIR__ . '/../../MockClasses/RootConstructor.php');
        $reflectionClass->method('getNamespaceName')->willReturn('MockClasses');

        $reflectionTokenResolver = new ReflectionUseResolver();

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
        $reflectionClass->method('getName')->willReturn('MockClasses\RootConstructor');
        $reflectionClass->method('getFileName')->willReturn('invalid-file-name');

        $reflectionTokenResolver = new ReflectionUseResolver();
        $reflectionTokenResolver->parseToken($reflectionClass);
    }

    /**
     * @throws Exception
     */
    public function testResolveInternalFunction(): void
    {
        $reflectionTokenResolver = new ReflectionUseResolver();
        $useStatementsReflection = $reflectionTokenResolver->resolve('DateTime');

        $this->assertNull($useStatementsReflection);
    }
}
