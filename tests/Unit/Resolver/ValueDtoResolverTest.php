<?php

declare(strict_types=1);

namespace Unit\Resolver;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Interface\ValueDtoInterface;
use Wundii\DataMapper\Resolver\DtoValueResolver;

class ValueDtoResolverTest extends TestCase
{
    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testResolve()
    {
        $typeDto = $this->createMock(ValueDtoInterface::class);
        $typeDto->method('getValue')->willReturn('testValue');
        $resolver = new DtoValueResolver();

        $this->assertSame('testValue', $resolver->resolve($typeDto));
    }
}
