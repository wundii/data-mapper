<?php

declare(strict_types=1);

namespace DataMapper\Tests\Unit\Reflection;

use DataMapper\Reflection\UseStatementReflection;
use DataMapper\Reflection\UseStatementsReflection;
use PHPUnit\Framework\TestCase;

class UseStatementsReflectionTest extends TestCase
{
    public function testFind(): void
    {
        $reflection = new UseStatementsReflection([
            new UseStatementReflection('Symfony\Contracts\HttpClient\ResponseInterface', 'ResponseInterface'),
            new UseStatementReflection('DataMapper\Tests\MockClasses\ItemConstructor', 'ItemConstructor'),
        ]);

        $this->assertSame('Symfony\Contracts\HttpClient\ResponseInterface', $reflection->find('ResponseInterface'));
        $this->assertSame('DataMapper\Tests\MockClasses\ItemConstructor', $reflection->find('ItemConstructor'));
        $this->assertSame('DataMapper\Tests\MockClasses\ItemConstructor', $reflection->find('ITEMCONSTRUCTOR'));
        $this->assertNull($reflection->find('NotFound'));
    }
}
