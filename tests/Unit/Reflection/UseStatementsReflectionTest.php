<?php

declare(strict_types=1);

namespace Unit\Reflection;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Dto\UseStatementDto;
use Wundii\DataMapper\Dto\UseStatementsDto;

class UseStatementsReflectionTest extends TestCase
{
    public function testFind(): void
    {
        $reflection = new UseStatementsDto(
            'MockClasses',
            [
                new UseStatementDto('Symfony\Contracts\HttpClient\ResponseInterface', 'ResponseInterface'),
                new UseStatementDto('MockClasses\ItemConstructor', 'ItemConstructor'),
            ]
        );

        $this->assertSame('MockClasses', $reflection->getNamespaceName());

        $expectedUseStatements = [
            new UseStatementDto('Symfony\Contracts\HttpClient\ResponseInterface', 'ResponseInterface'),
            new UseStatementDto('MockClasses\ItemConstructor', 'ItemConstructor'),
        ];
        $this->assertEquals($expectedUseStatements, $reflection->getUseStatements());

        $this->assertSame('Symfony\Contracts\HttpClient\ResponseInterface', $reflection->findClassString('ResponseInterface'));
        $this->assertSame('MockClasses\ItemConstructor', $reflection->findClassString('ItemConstructor'));
        $this->assertSame('MockClasses\ItemConstructor', $reflection->findClassString('ITEMCONSTRUCTOR'));
        $this->assertSame('MockClasses\TokenResolver', $reflection->findClassString('TokenResolver'));
        $this->assertNull($reflection->findClassString('NotFound'));
    }
}
