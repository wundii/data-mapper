<?php

declare(strict_types=1);

namespace Integration;

use DateTime;
use DateTimeZone;
use Integration\Objects\Types\TypeDateTimeConstructor;
use Integration\Objects\Types\TypeDateTimeProperty;
use Integration\Objects\Types\TypeDateTimeSetter;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;

class SpecialCaseDateTimeTest extends TestCase
{
    public function testConstructorFromIsoString(): void
    {
        $array = [
            'name' => 'test',
            'createdAt' => '2025-06-15T14:30:00+02:00',
        ];

        $result = $this->dataMapper(ApproachEnum::CONSTRUCTOR)->array($array, TypeDateTimeConstructor::class);

        $this->assertInstanceOf(TypeDateTimeConstructor::class, $result);
        $this->assertSame('test', $result->name);
        $this->assertInstanceOf(DateTime::class, $result->createdAt);
        $this->assertSame('2025-06-15', $result->createdAt->format('Y-m-d'));
        $this->assertSame('14:30:00', $result->createdAt->format('H:i:s'));
    }

    public function testConstructorFromSerializedRepresentation(): void
    {
        $original = new DateTime('2025-06-15T14:30:00', new DateTimeZone('Europe/Berlin'));

        $array = [
            'name' => 'test',
            'createdAt' => json_decode(json_encode($original), true),
        ];

        $result = $this->dataMapper(ApproachEnum::CONSTRUCTOR)->array($array, TypeDateTimeConstructor::class);

        $this->assertInstanceOf(TypeDateTimeConstructor::class, $result);
        $this->assertSame('test', $result->name);
        $this->assertInstanceOf(DateTime::class, $result->createdAt);
        $this->assertSame('2025-06-15', $result->createdAt->format('Y-m-d'));
        $this->assertSame('14:30:00', $result->createdAt->format('H:i:s'));
    }

    public function testPropertyFromIsoString(): void
    {
        $array = [
            'name' => 'test',
            'createdAt' => '2025-06-15T14:30:00+02:00',
        ];

        $result = $this->dataMapper(ApproachEnum::PROPERTY)->array($array, TypeDateTimeProperty::class);

        $this->assertInstanceOf(TypeDateTimeProperty::class, $result);
        $this->assertSame('test', $result->name);
        $this->assertInstanceOf(DateTime::class, $result->createdAt);
        $this->assertSame('2025-06-15', $result->createdAt->format('Y-m-d'));
        $this->assertSame('14:30:00', $result->createdAt->format('H:i:s'));
    }

    public function testPropertyFromSerializedRepresentation(): void
    {
        $original = new DateTime('2025-06-15T14:30:00', new DateTimeZone('Europe/Berlin'));

        $array = [
            'name' => 'test',
            'createdAt' => json_decode(json_encode($original), true),
        ];

        $result = $this->dataMapper(ApproachEnum::PROPERTY)->array($array, TypeDateTimeProperty::class);

        $this->assertInstanceOf(TypeDateTimeProperty::class, $result);
        $this->assertSame('test', $result->name);
        $this->assertInstanceOf(DateTime::class, $result->createdAt);
        $this->assertSame('2025-06-15', $result->createdAt->format('Y-m-d'));
        $this->assertSame('14:30:00', $result->createdAt->format('H:i:s'));
    }

    public function testSetterFromIsoString(): void
    {
        $array = [
            'name' => 'test',
            'createdAt' => '2025-06-15T14:30:00+02:00',
        ];

        $result = $this->dataMapper(ApproachEnum::SETTER)->array($array, TypeDateTimeSetter::class);

        $this->assertInstanceOf(TypeDateTimeSetter::class, $result);
        $this->assertSame('test', $result->getName());
        $this->assertInstanceOf(DateTime::class, $result->getCreatedAt());
        $this->assertSame('2025-06-15', $result->getCreatedAt()->format('Y-m-d'));
        $this->assertSame('14:30:00', $result->getCreatedAt()->format('H:i:s'));
    }

    public function testSetterFromSerializedRepresentation(): void
    {
        $original = new DateTime('2025-06-15T14:30:00', new DateTimeZone('Europe/Berlin'));

        $array = [
            'name' => 'test',
            'createdAt' => json_decode(json_encode($original), true),
        ];

        $result = $this->dataMapper(ApproachEnum::SETTER)->array($array, TypeDateTimeSetter::class);

        $this->assertInstanceOf(TypeDateTimeSetter::class, $result);
        $this->assertSame('test', $result->getName());
        $this->assertInstanceOf(DateTime::class, $result->getCreatedAt());
        $this->assertSame('2025-06-15', $result->getCreatedAt()->format('Y-m-d'));
        $this->assertSame('14:30:00', $result->getCreatedAt()->format('H:i:s'));
    }

    private function dataMapper(ApproachEnum $approach): DataMapper
    {
        $dataConfig = new DataConfig($approach);
        return new DataMapper($dataConfig);
    }
}
