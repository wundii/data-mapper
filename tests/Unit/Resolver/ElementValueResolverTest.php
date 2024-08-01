<?php

declare(strict_types=1);

namespace Unit\Resolver;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Interface\ElementValueInterface;
use Wundii\DataMapper\Resolver\ElementValueResolver;

class ElementValueResolverTest extends TestCase
{
    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testResolve()
    {
        $elementData = $this->createMock(ElementValueInterface::class);
        $elementData->method('getValue')->willReturn('testValue');
        $resolver = new ElementValueResolver();

        $this->assertSame('testValue', $resolver->resolve($elementData));
    }
}
