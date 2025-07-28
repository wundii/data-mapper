<?php

declare(strict_types=1);

namespace Integration;

use Integration\Objects\ApproachBasic\BaseMix;
use Integration\Objects\Types\TypeString;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;

class CsvTest extends TestCase
{
    public function dataMapper(ApproachEnum $approachEnum = ApproachEnum::PROPERTY): DataMapper
    {
        $dataConfig = new DataConfig($approachEnum);
        return new DataMapper($dataConfig);
    }

    public function testListOfStringsDefaultContent(): void
    {
        $csv = "string\n" .
            "Nostromo\n" .
            "Weyland-Yutani\n";

        $return = $this->dataMapper()->csv($csv, TypeString::class);

        $expected = [
            new TypeString('Nostromo'),
            new TypeString('Weyland-Yutani'),
        ];

        $this->assertCount(2, $return);
        $this->assertEquals($expected, $return);
    }

    public function testListOfStringsDefaultFile(): void
    {
        $csv = "string\n" .
            "Nostromo\n" .
            "Weyland-Yutani\n";
        $tempPath = tempnam(sys_get_temp_dir(), 'CSV_');
        file_put_contents($tempPath, $csv);

        $return = $this->dataMapper()->csv($tempPath, TypeString::class);

        $expected = [
            new TypeString('Nostromo'),
            new TypeString('Weyland-Yutani'),
        ];

        $this->assertFileExists($tempPath);
        $this->assertCount(2, $return);
        $this->assertEquals($expected, $return);

        unlink($tempPath);
    }

    public function testListOfStringsWithDescriptionLine(): void
    {
        $csv = "description\n" .
            "string\n" .
            "Nostromo\n" .
            "Weyland-Yutani\n";

        $return = $this->dataMapper()->csv(
            $csv,
            TypeString::class,
            headerLine: 2,
            firstLine: 3,
        );

        $expected = [
            new TypeString('Nostromo'),
            new TypeString('Weyland-Yutani'),
        ];

        $this->assertCount(2, $return);
        $this->assertEquals($expected, $return);
    }

    public function testListOfStringsStartFromThirdLine(): void
    {
        $csv = "string\n" .
            "ignoreLine\n" .
            "Nostromo\n" .
            "Weyland-Yutani\n";

        $return = $this->dataMapper()->csv(
            $csv,
            TypeString::class,
            firstLine: 3,
        );

        $expected = [
            new TypeString('Nostromo'),
            new TypeString('Weyland-Yutani'),
        ];

        $this->assertCount(2, $return);
        $this->assertEquals($expected, $return);
    }

    public function testListOfStringsWithDelimiter(): void
    {
        $csv = "AMOUNT,name\n" .
            "1000,Nostromo\n" .
            "1001,Weyland-Yutani\n";

        $return = $this->dataMapper(ApproachEnum::CONSTRUCTOR)->csv(
            $csv,
            BaseMix::class,
        );

        $expected = [
            new BaseMix(1000, 'Nostromo'),
            new BaseMix(1001, 'Weyland-Yutani'),
        ];

        $this->assertCount(2, $return);
        $this->assertEquals($expected, $return);
    }

    public function testListOfStringsWithCustomDelimiter(): void
    {
        $csv = "AMOUNT;name\n" .
            "1000;Nostromo\n" .
            "1001;Weyland-Yutani\n";

        $return = $this->dataMapper(ApproachEnum::CONSTRUCTOR)->csv(
            $csv,
            BaseMix::class,
            separator: ';',
        );

        $expected = [
            new BaseMix(1000, 'Nostromo'),
            new BaseMix(1001, 'Weyland-Yutani'),
        ];

        $this->assertCount(2, $return);
        $this->assertEquals($expected, $return);
    }

    public function testListOfStringsWithEnclosure(): void
    {
        $csv = "\"AMOUNT\",\"name\"\n" .
            "1000,\"Nostromo\"\n" .
            "1001,\"Weyland-Yutani\"\n";

        $return = $this->dataMapper(ApproachEnum::CONSTRUCTOR)->csv(
            $csv,
            BaseMix::class,
        );

        $expected = [
            new BaseMix(1000, 'Nostromo'),
            new BaseMix(1001, 'Weyland-Yutani'),
        ];

        $this->assertCount(2, $return);
        $this->assertEquals($expected, $return);
    }

    public function testListOfStringsWithCustomEnclosure(): void
    {
        $csv = "'AMOUNT','name'\n" .
            "1000,'Nostromo'\n" .
            "1001,'Weyland-Yutani'\n";

        $return = $this->dataMapper(ApproachEnum::CONSTRUCTOR)->csv(
            $csv,
            BaseMix::class,
            enclosure: "'",
        );

        $expected = [
            new BaseMix(1000, 'Nostromo'),
            new BaseMix(1001, 'Weyland-Yutani'),
        ];

        $this->assertCount(2, $return);
        $this->assertEquals($expected, $return);
    }

    public function testListOfStringsWithUnsortedColumns(): void
    {
        $csv = "name,AMOUNT\n" .
            "Nostromo,1000\n" .
            "Weyland-Yutani,1001\n";

        $return = $this->dataMapper(ApproachEnum::CONSTRUCTOR)->csv(
            $csv,
            BaseMix::class,
        );

        $expected = [
            new BaseMix(1000, 'Nostromo'),
            new BaseMix(1001, 'Weyland-Yutani'),
        ];

        $this->assertCount(2, $return);
        $this->assertEquals($expected, $return);
    }
}
