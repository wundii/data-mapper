<?php

declare(strict_types=1);

namespace Unit\Elements;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Elements\DataInt;

class DataIntTest extends TestCase
{
    public function testInstanceWithInt(): void
    {
        $dataInt = new DataInt(0);
        $this->assertSame(0, $dataInt->getValue());

        $dataInt = new DataInt(999);
        $this->assertSame(999, $dataInt->getValue());
    }

    public function testInstanceWithFloat(): void
    {
        $dataInt = new DataInt(12.34);
        $this->assertSame(12, $dataInt->getValue());

        $dataInt = new DataInt(12.64);
        $this->assertSame(12, $dataInt->getValue());
    }

    public function testInstanceWithString(): void
    {
        $dataInt = new DataInt('0');
        $this->assertSame(0, $dataInt->getValue());

        $dataInt = new DataInt('12.34');
        $this->assertSame(12, $dataInt->getValue());

        $dataInt = new DataInt('12.64');
        $this->assertSame(12, $dataInt->getValue());

        $dataInt = new DataInt('999');
        $this->assertSame(999, $dataInt->getValue());
    }

    public function testInstanceToString(): void
    {
        $dataInt = new DataInt(333);
        $this->assertSame('333', (string) $dataInt);
    }
}
