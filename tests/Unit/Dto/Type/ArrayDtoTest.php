<?php

declare(strict_types=1);

namespace Unit\Dto\Type;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Dto\Type\ArrayDto;

class ArrayDtoTest extends TestCase
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

        $arrayDto = new ArrayDto($array);
        $this->assertSame('value1, value2, value3', (string) $arrayDto);
    }
}
