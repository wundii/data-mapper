<?php

declare(strict_types=1);

namespace Integration;

use Integration\Objects\Types\TypeString;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;

class ArrayListsTest extends TestCase
{
    public function dataMapper(): DataMapper
    {
        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        return new DataMapper($dataConfig);
    }

    public function testListOfStringsWithIntKeys(): void
    {
        $array = [
            [
                'string' => 'Nostromo',
            ],
            [
                'string' => 'Weyland-Yutani',
            ],
        ];

        $return = $this->dataMapper()->array($array, TypeString::class);

        $expected = [
            new TypeString('Nostromo'),
            new TypeString('Weyland-Yutani'),
        ];

        $this->assertCount(2, $return);
        $this->assertEquals($expected, $return);
    }

    public function testListOfStringsWithStringKeys(): void
    {
        $array = [
            'a' => [
                'string' => 'Nostromo',
            ],
            'b' => [
                'string' => 'Weyland-Yutani',
            ],
        ];

        $return = $this->dataMapper()->array($array, TypeString::class);

        $expected = [
            'a' => new TypeString('Nostromo'),
            'b' => new TypeString('Weyland-Yutani'),
        ];

        $this->assertCount(2, $return);
        $this->assertEquals($expected, $return);
    }

    public function testListOfStringsWithBoths(): void
    {
        $array = [
            0 => [
                'string' => 'Nostromo',
            ],
            'a' => [
                'string' => 'Weyland-Yutani',
            ],
        ];

        $return = $this->dataMapper()->array($array, TypeString::class);

        $expected = [
            0 => new TypeString('Nostromo'),
            'a' => new TypeString('Weyland-Yutani'),
        ];

        $this->assertCount(2, $return);
        $this->assertEquals($expected, $return);
    }
}
