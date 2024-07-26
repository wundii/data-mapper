<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration;

use DataMapper\DataConfig;
use DataMapper\DataMapper;
use DataMapper\Enum\ApproachEnum;
use DateTime;
use DateTimeInterface;
use DataMapper\Tests\Integration\Objects\ClassMapDirectValue\DateTimeAlias;
use DataMapper\Tests\Integration\Objects\ClassMapDirectValue\DateTimeBasic;
use PHPUnit\Framework\TestCase;

class XmlClassMapDirectValueTest extends TestCase
{
    public function testDateTimeBasic(): void
    {
        $file = __DIR__ . '/XmlFiles/ClassMapDirectValueDateTime.xml';

        $dataConfig = new DataConfig(
            ApproachEnum::PROPERTY,
            classMap: [
                DateTimeInterface::class => DateTime::class,
            ],
        );
        $dataMapper = new DataMapper($dataConfig);
        $return = $dataMapper->xml(file_get_contents($file), DateTimeBasic::class);

        $expected = new DateTimeBasic();
        $expected->created = new DateTime('2024-07-02T09:05:50.131+02:00');

        $this->assertInstanceOf(DateTimeBasic::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testDateTimeAlias(): void
    {
        $file = __DIR__ . '/XmlFiles/ClassMapDirectValueDateTime.xml';

        $dataConfig = new DataConfig(
            ApproachEnum::PROPERTY,
            classMap: [
                DateTimeInterface::class => DateTime::class,
            ],
        );
        $dataMapper = new DataMapper($dataConfig);
        $return = $dataMapper->xml(file_get_contents($file), DateTimeAlias::class);

        $expected = new DateTimeAlias();
        $expected->created = new DateTime('2024-07-02T09:05:50.131+02:00');

        $this->assertInstanceOf(DateTimeAlias::class, $return);
        $this->assertEquals($expected, $return);
    }
}
