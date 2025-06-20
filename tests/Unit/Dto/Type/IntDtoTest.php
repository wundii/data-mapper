<?php

declare(strict_types=1);

namespace Unit\Dto\Type;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Dto\Type\IntDto;

class IntDtoTest extends TestCase
{
    public function testInstanceWithInt(): void
    {
        $intDto = new IntDto(0);
        $this->assertSame(0, $intDto->getValue());

        $intDto = new IntDto(999);
        $this->assertSame(999, $intDto->getValue());
    }

    public function testInstanceWithFloat(): void
    {
        $intDto = new IntDto(12.34);
        $this->assertSame(12, $intDto->getValue());

        $intDto = new IntDto(12.64);
        $this->assertSame(12, $intDto->getValue());
    }

    public function testInstanceWithString(): void
    {
        $intDto = new IntDto('0');
        $this->assertSame(0, $intDto->getValue());

        $intDto = new IntDto('12.34');
        $this->assertSame(12, $intDto->getValue());

        $intDto = new IntDto('12.64');
        $this->assertSame(12, $intDto->getValue());

        $intDto = new IntDto('999');
        $this->assertSame(999, $intDto->getValue());
    }

    public function testInstanceToString(): void
    {
        $intDto = new IntDto(333);
        $this->assertSame('333', (string) $intDto);
    }
}
