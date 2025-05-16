<?php

declare(strict_types=1);

namespace Unit\Elements;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Elements\DataFloat;

class DataFloatTest extends TestCase
{
    public function testNumberFormatWithInteger()
    {
        $dataFloat = new DataFloat(12345);
        $this->assertEquals(12345.0, $dataFloat->stringToFloat());
    }

    public function testNumberFormatWithFloat()
    {
        $dataFloat = new DataFloat(123.45);
        $this->assertEquals(123.45, $dataFloat->stringToFloat());
    }

    public function testNumberFormatWithStringContainingComma()
    {
        $dataFloat = new DataFloat('1,234.56');
        $this->assertEquals(1234.56, $dataFloat->stringToFloat());
    }

    public function testNumberFormatWithStringContainingPeriod()
    {
        $dataFloat = new DataFloat('1.234,56');
        $this->assertEquals(1234.56, $dataFloat->stringToFloat());
    }

    public function testNumberFormatWithStringContainingMixedSeparators()
    {
        $dataFloat = new DataFloat('1.234,56.78');
        $this->assertEquals(1234.5678, $dataFloat->stringToFloat());
    }

    public function testNumberFormatWithStringContainingLetters()
    {
        $dataFloat = new DataFloat('1a2b3c4d.56e7f8g');
        $this->assertEquals(1234.5678, $dataFloat->stringToFloat());
    }

    public function testNumberFormatWithStringContainingLeadingZeros()
    {
        $dataFloat = new DataFloat('000123.45');
        $this->assertEquals(123.45, $dataFloat->stringToFloat());
    }

    public function testNumberFormatWithStringContainingTrailingZeros()
    {
        $dataFloat = new DataFloat('123.4500');
        $this->assertEquals(123.45, $dataFloat->stringToFloat());
    }

    public function testNumberFormatWithStringContainingLeadingPlusSign()
    {
        $dataFloat = new DataFloat('+123.45');
        $this->assertEquals(123.45, $dataFloat->stringToFloat());
    }

    public function testNumberFormatWithStringContainingLeadingMinusSignDecimal0RoundDown()
    {
        $dataFloat = new DataFloat('-123.49');
        $this->assertEquals(-123.49, $dataFloat->stringToFloat());
    }

    public function testNumberFormatWithStringContainingLeadingMinusSignDecimalRoundUp()
    {
        $dataFloat = new DataFloat('123.50');
        $this->assertEquals(123.5, $dataFloat->stringToFloat());
    }

    public function testInstanceWithFloat(): void
    {
        $dataFloat = new DataFloat(12.34);
        $this->assertSame(12.34, $dataFloat->getValue());
    }

    public function testInstanceWithInt(): void
    {
        $dataFloat = new DataFloat(0);
        $this->assertSame(0.0, $dataFloat->getValue());

        $dataFloat = new DataFloat(999);
        $this->assertSame(999.0, $dataFloat->getValue());
    }

    public function testInstanceWithString(): void
    {
        $dataFloat = new DataFloat('0');
        $this->assertSame(0.0, $dataFloat->getValue());

        $dataFloat = new DataFloat('12.34');
        $this->assertSame(12.34, $dataFloat->getValue());

        $dataFloat = new DataFloat('12,34');
        $this->assertSame(12.34, $dataFloat->getValue());

        $dataFloat = new DataFloat('999');
        $this->assertSame(999.0, $dataFloat->getValue());
    }

    public function testInstanceToString(): void
    {
        $dataFloat = new DataFloat(333.33);
        $this->assertSame('333.33', (string) $dataFloat);

        $dataFloat = new DataFloat('333,33');
        $this->assertSame('333.33', (string) $dataFloat);
    }
}
