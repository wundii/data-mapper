<?php

declare(strict_types=1);

namespace Integration;

use DateTime;
use DateTimeInterface;
use Integration\Objects\ClassMapDirectValue\DateTimeAlias;
use Integration\Objects\ClassMapDirectValue\DateTimeBasic;
use Integration\Objects\ClassMapDirectValue\EnumBasic;
use Integration\Objects\Types\TypeEnumInt;
use Integration\Objects\Types\TypeEnumString;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;

class ArrayClassMapDirectValueTest extends TestCase
{
    public function testDateTimeBasic(): void
    {
        $array = [
            'created' => '2024-07-02T09:05:50.131+02:00',
        ];

        $dataConfig = new DataConfig(
            ApproachEnum::PROPERTY,
            classMap: [
                DateTimeInterface::class => DateTime::class,
            ],
        );
        $dataMapper = new DataMapper($dataConfig);
        $return = $dataMapper->array($array, DateTimeBasic::class);

        $expected = new DateTimeBasic();
        $expected->created = new DateTime('2024-07-02T09:05:50.131+02:00');

        $this->assertInstanceOf(DateTimeBasic::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testDateTimeAlias(): void
    {
        $array = [
            'created' => '2024-07-02T09:05:50.131+02:00',
        ];

        $dataConfig = new DataConfig(
            ApproachEnum::PROPERTY,
            classMap: [
                DateTimeInterface::class => DateTime::class,
            ],
        );
        $dataMapper = new DataMapper($dataConfig);
        $return = $dataMapper->array($array, DateTimeAlias::class);

        $expected = new DateTimeAlias();
        $expected->created = new DateTime('2024-07-02T09:05:50.131+02:00');

        $this->assertInstanceOf(DateTimeAlias::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testEnumBasic(): void
    {
        $array = [
            'enumString' => 'Tokyo',
            'enumInt' => 3,
        ];

        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        $dataMapper = new DataMapper($dataConfig);
        $return = $dataMapper->array($array, EnumBasic::class);

        $expected = new EnumBasic();
        $expected->enumString = TypeEnumString::TOKYO;
        $expected->enumInt = TypeEnumInt::LONDON;

        $this->assertInstanceOf(EnumBasic::class, $return);
        $this->assertEquals($expected, $return);
    }
}
