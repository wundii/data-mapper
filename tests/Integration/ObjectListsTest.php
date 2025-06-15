<?php

declare(strict_types=1);

namespace Integration;

use Integration\Objects\Serialize\ToArray;
use Integration\Objects\Types\TypeString;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;

class ObjectListsTest extends TestCase
{
    public function dataMapper(): DataMapper
    {
        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        return new DataMapper($dataConfig);
    }

    public function testListOfStringsWithIntKeys(): void
    {
        $array = [
            new ToArray('Nostromo'),
            new ToArray('Weyland-Yutani'),
        ];

        $return = $this->dataMapper()->object($array, TypeString::class);

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
            'a' => new ToArray('Nostromo'),
            'b' => new ToArray('Weyland-Yutani'),
        ];

        $return = $this->dataMapper()->object($array, TypeString::class);

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
            0 => new ToArray('Nostromo'),
            'a' => new ToArray('Weyland-Yutani'),
        ];

        $return = $this->dataMapper()->object($array, TypeString::class);

        $expected = [
            0 => new TypeString('Nostromo'),
            'a' => new TypeString('Weyland-Yutani'),
        ];

        $this->assertCount(2, $return);
        $this->assertEquals($expected, $return);
    }
}
