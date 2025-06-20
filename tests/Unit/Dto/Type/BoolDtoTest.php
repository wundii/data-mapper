<?php

declare(strict_types=1);

namespace Unit\Dto\Type;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Dto\Type\BoolDto;

class BoolDtoTest extends TestCase
{
    public function testInstanceWithBool(): void
    {
        $boolDto = new BoolDto(false);
        $this->assertFalse($boolDto->getValue());

        $boolDto = new BoolDto(true);
        $this->assertTrue($boolDto->getValue());
    }

    public function testInstanceWithInt(): void
    {
        $boolDto = new BoolDto(0);
        $this->assertFalse($boolDto->getValue());

        $boolDto = new BoolDto(999);
        $this->assertFalse($boolDto->getValue());

        $boolDto = new BoolDto(1);
        $this->assertTrue($boolDto->getValue());
    }

    public function testInstanceWithString(): void
    {
        $boolDto = new BoolDto('false');
        $this->assertFalse($boolDto->getValue());

        $boolDto = new BoolDto('0');
        $this->assertFalse($boolDto->getValue());

        $boolDto = new BoolDto('999');
        $this->assertFalse($boolDto->getValue());

        $boolDto = new BoolDto('off');
        $this->assertFalse($boolDto->getValue());

        $boolDto = new BoolDto('no');
        $this->assertFalse($boolDto->getValue());

        $boolDto = new BoolDto('true');
        $this->assertTrue($boolDto->getValue());

        $boolDto = new BoolDto('1');
        $this->assertTrue($boolDto->getValue());

        $boolDto = new BoolDto('on');
        $this->assertTrue($boolDto->getValue());

        $boolDto = new BoolDto('yes');
        $this->assertTrue($boolDto->getValue());
    }

    public function testInstanceToString(): void
    {
        $boolDto = new BoolDto(true);
        $this->assertSame('true', (string) $boolDto);

        $boolDto = new BoolDto(false);
        $this->assertSame('false', (string) $boolDto);
    }
}
