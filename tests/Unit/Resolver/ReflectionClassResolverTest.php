<?php

declare(strict_types=1);

namespace Unit\Resolver;

use MockClasses\TestClass;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Resolver\ReflectionClassResolver;
use Wundii\DataMapper\Resolver\ReflectionObjectResolver;
use Wundii\DataMapper\Resolver\ReflectionUseResolver;

class ReflectionClassResolverTest extends TestCase
{
    public function testNewResolver(): void
    {
        $object = TestClass::class;

        $useStatementsDto = ReflectionUseResolver::resolveObject($object);
        $resolver = new ReflectionClassResolver($useStatementsDto);

        dd($resolver->resolve($object));

        $this->assertInstanceOf(ReflectionObjectResolver::class, $resolver);
    }
}
