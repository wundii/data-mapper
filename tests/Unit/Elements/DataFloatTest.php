<?php

declare(strict_types=1);

namespace Unit\Elements;

use Wundii\DataMapper\Elements\DataFloat;
use PHPUnit\Framework\TestCase;

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
}
