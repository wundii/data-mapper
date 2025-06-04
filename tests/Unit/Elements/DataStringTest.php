<?php

declare(strict_types=1);

namespace Unit\Elements;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Elements\DataString;

class DataStringTest extends TestCase
{
    public function testInstanceWithString(): void
    {
        $dataString = new DataString('');
        $this->assertSame('', $dataString->getValue());

        $dataString = new DataString('foo');
        $this->assertSame('foo', $dataString->getValue());
    }

    public function testInstanceToString(): void
    {
        $dataString = new DataString('bar');
        $this->assertSame('bar', (string) $dataString);
    }
}
