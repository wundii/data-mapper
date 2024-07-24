<?php

declare(strict_types=1);

namespace DataMapper\Tests\Unit\Resolver;

use DataMapper\Reflection\UseStatementReflection;
use DataMapper\Reflection\UseStatementsReflection;
use DataMapper\Resolver\ReflectionTokenResolver;
use DataMapper\Tests\MockClasses\TokenResolver;
use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ReflectionTokenResolverTest extends TestCase
{
    public function testBasename(): void
    {
        $reflectionTokenResolver = new ReflectionTokenResolver();

        $basename = $reflectionTokenResolver->basename('DataMapper\Tests\MockClasses\RootConstructor');

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
        $reflectionClass->method('getName')->willReturn('DataMapper\Tests\MockClasses\RootConstructor');
        $reflectionClass->method('getFileName')->willReturn(__DIR__ . '/../../MockClasses/RootConstructor.php');
        $reflectionClass->method('getNamespaceName')->willReturn('');

        $reflectionTokenResolver = new ReflectionTokenResolver();

        $expected = new UseStatementsReflection(
            null,
            [
                new UseStatementReflection('DataMapper\Tests\MockClasses\RootConstructor', 'RootConstructor'),
                new UseStatementReflection('DataMapper\Tests\MockClasses\Sub\SubItemConstructor', 'SubItemConstructor'),
            ],
        );

        $this->assertEquals($expected, $reflectionTokenResolver->parseToken($reflectionClass));

        $reflectionClass = $this->createMock(ReflectionClass::class);
        $reflectionClass->method('getName')->willReturn('DataMapper\Tests\MockClasses\RootConstructor');
        $reflectionClass->method('getFileName')->willReturn(__DIR__ . '/../../MockClasses/RootConstructor.php');
        $reflectionClass->method('getNamespaceName')->willReturn('DataMapper\Tests\MockClasses');

        $reflectionTokenResolver = new ReflectionTokenResolver();

        $expected = new UseStatementsReflection(
            'DataMapper\Tests\MockClasses',
            [
                new UseStatementReflection('DataMapper\Tests\MockClasses\RootConstructor', 'RootConstructor'),
                new UseStatementReflection('DataMapper\Tests\MockClasses\Sub\SubItemConstructor', 'SubItemConstructor'),
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
        $this->expectExceptionMessage('Could not read file content from invalid-file-name');

        $reflectionClass = $this->createMock(ReflectionClass::class);
        $reflectionClass->method('getName')->willReturn('DataMapper\Tests\MockClasses\RootConstructor');
        $reflectionClass->method('getFileName')->willReturn('invalid-file-name');

        /**
         * file_get_contents will return false, because the file does not exist
         */
        set_error_handler(function () {});

        $reflectionTokenResolver = new ReflectionTokenResolver();
        $reflectionTokenResolver->parseToken($reflectionClass);

        restore_error_handler();
    }

    /**
     * @throws Exception
     */
    public function testResolve(): void
    {
        $reflectionTokenResolver = new ReflectionTokenResolver();
        $useStatementsReflection = $reflectionTokenResolver->resolve(TokenResolver::class);

        $expected = new UseStatementsReflection(
            'DataMapper\Tests\MockClasses',
            [
                new UseStatementReflection('DataMapper\Tests\MockClasses\TokenResolver', 'TokenResolver'),
                new UseStatementReflection('DataMapper\Tests\MockClasses\Sub\SubItemConstructor', 'SubItemConstructor'),
                new UseStatementReflection('DataMapper\Tests\MockClasses\ItemConstructor', 'CustomItemConstructor'),
            ],
        );

        $this->assertEquals($expected, $useStatementsReflection);
    }
}
