<?php

declare(strict_types=1);

namespace Unit\Elements;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Elements\DataArray;

class DataArrayTest extends TestCase
{
    public function testInstanceToString(): void
    {
        $array = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
            'key4' => 'value4',
            'key5' => 'value5',
        ];

        $dataArray = new DataArray($array);
        $this->assertSame('value1, value2, value3', (string) $dataArray);
    }
}
