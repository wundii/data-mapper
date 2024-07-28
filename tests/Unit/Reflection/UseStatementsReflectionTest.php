<?php

declare(strict_types=1);

namespace Unit\Reflection;

use Wundii\DataMapper\Reflection\UseStatementReflection;
use Wundii\DataMapper\Reflection\UseStatementsReflection;
use PHPUnit\Framework\TestCase;

class UseStatementsReflectionTest extends TestCase
{
    public function testFind(): void
    {
        $reflection = new UseStatementsReflection(
            'MockClasses',
            [
                new UseStatementReflection('Symfony\Contracts\HttpClient\ResponseInterface', 'ResponseInterface'),
                new UseStatementReflection('MockClasses\ItemConstructor', 'ItemConstructor'),
            ]
        );

        $this->assertSame('Symfony\Contracts\HttpClient\ResponseInterface', $reflection->find('ResponseInterface'));
        $this->assertSame('MockClasses\ItemConstructor', $reflection->find('ItemConstructor'));
        $this->assertSame('MockClasses\ItemConstructor', $reflection->find('ITEMCONSTRUCTOR'));
        $this->assertSame('MockClasses\TokenResolver', $reflection->find('TokenResolver'));
        $this->assertNull($reflection->find('NotFound'));
    }
}
