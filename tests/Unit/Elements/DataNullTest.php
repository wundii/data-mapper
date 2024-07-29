<?php

declare(strict_types=1);

namespace Unit\Elements;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Elements\DataNull;

class DataNullTest extends TestCase
{
    public function testInstanceToString(): void
    {
        $dataNull = new DataNull();
        $this->assertSame('null', (string) $dataNull);
    }
}
