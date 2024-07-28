<?php

declare(strict_types=1);

namespace Integration;

use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;
use DateTime;
use DateTimeInterface;
use Integration\Objects\ClassMapDirectValue\DateTimeAlias;
use Integration\Objects\ClassMapDirectValue\DateTimeBasic;
use Integration\Objects\ClassMapDirectValue\EnumBasic;
use Integration\Objects\Types\TypeEnumInt;
use Integration\Objects\Types\TypeEnumString;
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
        $dataMapper = new DataMapper();
        $return = $dataMapper->xml(file_get_contents($file), DateTimeBasic::class, $dataConfig);

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
        $dataMapper = new DataMapper();
        $return = $dataMapper->xml(file_get_contents($file), DateTimeAlias::class, $dataConfig);

        $expected = new DateTimeAlias();
        $expected->created = new DateTime('2024-07-02T09:05:50.131+02:00');

        $this->assertInstanceOf(DateTimeAlias::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testEnumBasic(): void
    {
        $file = __DIR__ . '/XmlFiles/ClassMapDirectValueEnum.xml';

        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        $dataMapper = new DataMapper();
        $return = $dataMapper->xml(file_get_contents($file), EnumBasic::class, $dataConfig);

        $expected = new EnumBasic();
        $expected->enumString = TypeEnumString::TOKYO;
        $expected->enumInt = TypeEnumInt::LONDON;

        $this->assertInstanceOf(EnumBasic::class, $return);
        $this->assertEquals($expected, $return);
    }
}
