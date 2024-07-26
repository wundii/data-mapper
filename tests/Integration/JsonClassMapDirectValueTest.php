<?php

declare(strict_types=1);

namespace Integration;

use DataMapper\DataConfig;
use DataMapper\DataMapper;
use DataMapper\Enum\ApproachEnum;
use DataMapper\Tests\Integration\Objects\ClassMapDirectValue\DateTimeAlias;
use DataMapper\Tests\Integration\Objects\ClassMapDirectValue\DateTimeBasic;
use DataMapper\Tests\Integration\Objects\ClassMapDirectValue\EnumBasic;
use DataMapper\Tests\Integration\Objects\Types\TypeEnumInt;
use DataMapper\Tests\Integration\Objects\Types\TypeEnumString;
use DateTime;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

class JsonClassMapDirectValueTest extends TestCase
{
    public function testDateTimeBasic(): void
    {
        $file = __DIR__ . '/JsonFiles/ClassMapDirectValueDateTime.json';

        $dataConfig = new DataConfig(
            ApproachEnum::PROPERTY,
            classMap: [
                DateTimeInterface::class => DateTime::class,
            ],
        );
        $dataMapper = new DataMapper();
        $return = $dataMapper->json(file_get_contents($file), DateTimeBasic::class, $dataConfig);

        $expected = new DateTimeBasic();
        $expected->created = new DateTime('2024-07-02T09:05:50.131+02:00');

        $this->assertInstanceOf(DateTimeBasic::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testDateTimeAlias(): void
    {
        $file = __DIR__ . '/JsonFiles/ClassMapDirectValueDateTime.json';

        $dataConfig = new DataConfig(
            ApproachEnum::PROPERTY,
            classMap: [
                DateTimeInterface::class => DateTime::class,
            ],
        );
        $dataMapper = new DataMapper();
        $return = $dataMapper->json(file_get_contents($file), DateTimeAlias::class, $dataConfig);

        $expected = new DateTimeAlias();
        $expected->created = new DateTime('2024-07-02T09:05:50.131+02:00');

        $this->assertInstanceOf(DateTimeAlias::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testEnumBasic(): void
    {
        $file = __DIR__ . '/JsonFiles/ClassMapDirectValueEnum.json';

        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        $dataMapper = new DataMapper();
        $return = $dataMapper->json(file_get_contents($file), EnumBasic::class, $dataConfig);

        $expected = new EnumBasic();
        $expected->enumString = TypeEnumString::TOKYO;
        $expected->enumInt = TypeEnumInt::LONDON;

        $this->assertInstanceOf(EnumBasic::class, $return);
        $this->assertEquals($expected, $return);
    }
}
