<?php

declare(strict_types=1);

namespace Unit\Resolver;

use Wundii\DataMapper\Interface\ElementArrayInterface;
use Wundii\DataMapper\Interface\ElementDataInterface;
use Wundii\DataMapper\Interface\ElementObjectInterface;
use Wundii\DataMapper\Resolver\ElementValueResolver;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ElementValueResolverTest extends TestCase
{
    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testResolveThrowsExceptionForElementObjectInterface()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ObjectElementInterface not supported');

        $elementData = $this->createMock(ElementObjectInterface::class);
        $resolver = new ElementValueResolver();
        $resolver->resolve($elementData);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testResolveThrowsExceptionForElementArrayInterface()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('DataObject not supported');

        $elementData = $this->createMock(ElementArrayInterface::class);
        $resolver = new ElementValueResolver();
        $resolver->resolve($elementData);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testResolveReturnsValueForElementDataInterface()
    {
        $expectedValue = 'testValue';

        $elementData = $this->createMock(ElementDataInterface::class);
        $elementData->method('getValue')->willReturn($expectedValue);

        $resolver = new ElementValueResolver();
        $result = $resolver->resolve($elementData);

        $this->assertEquals($expectedValue, $result);
    }
}
