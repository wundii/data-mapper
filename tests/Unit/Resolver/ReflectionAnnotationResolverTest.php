<?php

declare(strict_types=1);

namespace Unit\Resolver;

use PHPUnit\Framework\TestCase;
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

        $reflectionObjectResolver = new ReflectionAnnotationResolver(null);

        $this->assertSame($types, $reflectionObjectResolver->completeClassStrings($types));
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

        $reflectionObjectResolver = new ReflectionAnnotationResolver($useStatementsReflection);

        $this->assertSame($expected, $reflectionObjectResolver->completeClassStrings($types));
    }
}
