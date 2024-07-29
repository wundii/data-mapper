<?php

declare(strict_types=1);

namespace Unit\Elements;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Elements\DataFloat;

class DataFloatTest extends TestCase
{
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

        $dataFloat = new DataFloat('999');
        $this->assertSame(999.0, $dataFloat->getValue());
    }

    public function testInstanceToString(): void
    {
        $dataFloat = new DataFloat(333.33);
        $this->assertSame('333.33', (string) $dataFloat);
    }
}
