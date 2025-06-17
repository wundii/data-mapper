<?php

declare(strict_types=1);

namespace Integration;

use Integration\Objects\Types\TypeString;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;

class NeonListsTest extends TestCase
{
    public function dataMapper(): DataMapper
    {
        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        return new DataMapper($dataConfig);
    }

    public function testListOfStringsWithIntKeys(): void
    {
        $file = __DIR__ . '/NeonFiles/ListStrings01.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeString::class);

        $expected = [
            new TypeString('Nostromo'),
            new TypeString('Weyland-Yutani'),
        ];

        $this->assertCount(2, $return);
        $this->assertEquals($expected, $return);
    }

    public function testListOfStringsWithStringKeys(): void
    {
        $file = __DIR__ . '/NeonFiles/ListStrings02.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeString::class);

        $expected = [
            'a' => new TypeString('Nostromo'),
            'b' => new TypeString('Weyland-Yutani'),
        ];

        $this->assertCount(2, $return);
        $this->assertEquals($expected, $return);
    }

    public function testListOfStringsWithBoths(): void
    {
        $file = __DIR__ . '/NeonFiles/ListStrings03.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeString::class);

        $expected = [
            0 => new TypeString('Nostromo'),
            'a' => new TypeString('Weyland-Yutani'),
        ];

        $this->assertCount(2, $return);
        $this->assertEquals($expected, $return);
    }
}
