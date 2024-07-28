<?php

declare(strict_types=1);

namespace Unit\Elements;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Elements\DataBool;

class DataBoolTest extends TestCase
{
    public function testInstanceWithBool(): void
    {
        $dataBool = new DataBool(false);
        $this->assertFalse($dataBool->getValue());

        $dataBool = new DataBool(true);
        $this->assertTrue($dataBool->getValue());
    }

    public function testInstanceWithInt(): void
    {
        $dataBool = new DataBool(0);
        $this->assertFalse($dataBool->getValue());

        $dataBool = new DataBool(999);
        $this->assertFalse($dataBool->getValue());

        $dataBool = new DataBool(1);
        $this->assertTrue($dataBool->getValue());
    }

    public function testInstanceWithString(): void
    {
        $dataBool = new DataBool('false');
        $this->assertFalse($dataBool->getValue());

        $dataBool = new DataBool('0');
        $this->assertFalse($dataBool->getValue());

        $dataBool = new DataBool('999');
        $this->assertFalse($dataBool->getValue());

        $dataBool = new DataBool('off');
        $this->assertFalse($dataBool->getValue());

        $dataBool = new DataBool('no');
        $this->assertFalse($dataBool->getValue());

        $dataBool = new DataBool('true');
        $this->assertTrue($dataBool->getValue());

        $dataBool = new DataBool('1');
        $this->assertTrue($dataBool->getValue());

        $dataBool = new DataBool('on');
        $this->assertTrue($dataBool->getValue());

        $dataBool = new DataBool('yes');
        $this->assertTrue($dataBool->getValue());
    }
}
