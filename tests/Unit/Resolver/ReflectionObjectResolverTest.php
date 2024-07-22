<?php

declare(strict_types=1);

namespace DataMapper\Tests\Unit\Resolver;

use DataMapper\Reflection\ObjectReflection;
use DataMapper\Resolver\ReflectionObjectResolver;
use DataMapper\Tests\MockClasses\RootClassConstructor;
use Exception;
use PHPUnit\Framework\TestCase;

class ReflectionObjectResolverTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testSuccess(): void
    {
        $reflectionObjectResolver = new ReflectionObjectResolver();
        $objectReflection = $reflectionObjectResolver->resolve(RootClassConstructor::class);

        $this->assertInstanceOf(ObjectReflection::class, $objectReflection);
    }
}
