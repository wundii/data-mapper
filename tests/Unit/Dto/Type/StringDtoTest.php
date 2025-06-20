<?php

declare(strict_types=1);

namespace Unit\Dto\Type;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Dto\Type\StringDto;

class StringDtoTest extends TestCase
{
    public function testInstanceWithString(): void
    {
        $stringDto = new StringDto('');
        $this->assertSame('', $stringDto->getValue());

        $stringDto = new StringDto('foo');
        $this->assertSame('foo', $stringDto->getValue());
    }

    public function testInstanceToString(): void
    {
        $stringDto = new StringDto('bar');
        $this->assertSame('bar', (string) $stringDto);
    }
}
