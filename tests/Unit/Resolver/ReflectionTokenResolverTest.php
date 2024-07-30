<?php

declare(strict_types=1);

namespace Unit\Resolver;

use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Wundii\DataMapper\Reflection\UseStatementReflection;
use Wundii\DataMapper\Reflection\UseStatementsReflection;
use Wundii\DataMapper\Resolver\ReflectionTokenResolver;

class ReflectionTokenResolverTest extends TestCase
{
    public function testBasename(): void
    {
        $reflectionTokenResolver = new ReflectionTokenResolver();

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

        $reflectionTokenResolver = new ReflectionTokenResolver();

        $expected = new UseStatementsReflection(
            null,
            [
                new UseStatementReflection('MockClasses\RootConstructor', 'RootConstructor'),
                new UseStatementReflection('MockClasses\Sub\SubItemConstructor', 'SubItemConstructor'),
            ],
        );

        $this->assertEquals($expected, $reflectionTokenResolver->parseToken($reflectionClass));

        $reflectionClass = $this->createMock(ReflectionClass::class);
        $reflectionClass->method('getName')->willReturn('MockClasses\RootConstructor');
        $reflectionClass->method('getFileName')->willReturn(__DIR__ . '/../../MockClasses/RootConstructor.php');
        $reflectionClass->method('getNamespaceName')->willReturn('MockClasses');

        $reflectionTokenResolver = new ReflectionTokenResolver();

        $expected = new UseStatementsReflection(
            'MockClasses',
            [
                new UseStatementReflection('MockClasses\RootConstructor', 'RootConstructor'),
                new UseStatementReflection('MockClasses\Sub\SubItemConstructor', 'SubItemConstructor'),
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

        $reflectionTokenResolver = new ReflectionTokenResolver();
        $reflectionTokenResolver->parseToken($reflectionClass);
    }

    /**
     * @throws Exception
     */
    public function testResolveInternalFunction(): void
    {
        $reflectionTokenResolver = new ReflectionTokenResolver();
        $useStatementsReflection = $reflectionTokenResolver->resolve('DateTime');

        $expected = new UseStatementsReflection(null, []);

        $this->assertEquals($expected, $useStatementsReflection);
    }
}
