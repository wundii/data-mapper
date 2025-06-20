<?php

declare(strict_types=1);

namespace Unit\Dto\Type;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Dto\Type\FloatDto;

class FloatDtoTest extends TestCase
{
    public function testNumberFormatWithInteger()
    {
        $floatDto = new FloatDto(12345);
        $this->assertEquals(12345.0, $floatDto->stringToFloat());
    }

    public function testNumberFormatWithFloat()
    {
        $floatDto = new FloatDto(123.45);
        $this->assertEquals(123.45, $floatDto->stringToFloat());
    }

    public function testNumberFormatWithStringContainingComma()
    {
        $floatDto = new FloatDto('1,234.56');
        $this->assertEquals(1234.56, $floatDto->stringToFloat());
    }

    public function testNumberFormatWithStringContainingPeriod()
    {
        $floatDto = new FloatDto('1.234,56');
        $this->assertEquals(1234.56, $floatDto->stringToFloat());
    }

    public function testNumberFormatWithStringContainingMixedSeparators()
    {
        $floatDto = new FloatDto('1.234,56.78');
        $this->assertEquals(1234.5678, $floatDto->stringToFloat());
    }

    public function testNumberFormatWithStringContainingLetters()
    {
        $floatDto = new FloatDto('1a2b3c4d.56e7f8g');
        $this->assertEquals(1234.5678, $floatDto->stringToFloat());
    }

    public function testNumberFormatWithStringContainingLeadingZeros()
    {
        $floatDto = new FloatDto('000123.45');
        $this->assertEquals(123.45, $floatDto->stringToFloat());
    }

    public function testNumberFormatWithStringContainingTrailingZeros()
    {
        $floatDto = new FloatDto('123.4500');
        $this->assertEquals(123.45, $floatDto->stringToFloat());
    }

    public function testNumberFormatWithStringContainingLeadingPlusSign()
    {
        $floatDto = new FloatDto('+123.45');
        $this->assertEquals(123.45, $floatDto->stringToFloat());
    }

    public function testNumberFormatWithStringContainingLeadingMinusSignDecimal0RoundDown()
    {
        $floatDto = new FloatDto('-123.49');
        $this->assertEquals(-123.49, $floatDto->stringToFloat());
    }

    public function testNumberFormatWithStringContainingLeadingMinusSignDecimalRoundUp()
    {
        $floatDto = new FloatDto('123.50');
        $this->assertEquals(123.5, $floatDto->stringToFloat());
    }

    public function testInstanceWithFloat(): void
    {
        $floatDto = new FloatDto(12.34);
        $this->assertSame(12.34, $floatDto->getValue());
    }

    public function testInstanceWithInt(): void
    {
        $floatDto = new FloatDto(0);
        $this->assertSame(0.0, $floatDto->getValue());

        $floatDto = new FloatDto(999);
        $this->assertSame(999.0, $floatDto->getValue());
    }

    public function testInstanceWithString(): void
    {
        $floatDto = new FloatDto('0');
        $this->assertSame(0.0, $floatDto->getValue());

        $floatDto = new FloatDto('12.34');
        $this->assertSame(12.34, $floatDto->getValue());

        $floatDto = new FloatDto('12,34');
        $this->assertSame(12.34, $floatDto->getValue());

        $floatDto = new FloatDto('999');
        $this->assertSame(999.0, $floatDto->getValue());
    }

    public function testInstanceToString(): void
    {
        $floatDto = new FloatDto(333.33);
        $this->assertSame('333.33', (string) $floatDto);

        $floatDto = new FloatDto('333,33');
        $this->assertSame('333.33', (string) $floatDto);
    }
}
