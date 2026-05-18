<?php

declare(strict_types=1);

namespace Integration;

use DateTimeImmutable;
use DateTimeZone;
use Integration\Objects\Types\TypeDateTimeImmutable;
use Integration\Objects\Types\TypeDateTimeImmutableNested;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;

class SpecialCaseDateTimeImmutableTest extends TestCase
{
    private function dataMapper(): DataMapper
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        return new DataMapper($dataConfig);
    }

    public function testDateTimeImmutableFromIsoString(): void
    {
        $array = [
            'name' => 'test',
            'createdAt' => '2025-06-15T14:30:00+02:00',
        ];

        $result = $this->dataMapper()->array($array, TypeDateTimeImmutable::class);

        $this->assertInstanceOf(TypeDateTimeImmutable::class, $result);
        $this->assertSame('test', $result->name);
        $this->assertInstanceOf(DateTimeImmutable::class, $result->createdAt);
        $this->assertSame('2025-06-15', $result->createdAt->format('Y-m-d'));
        $this->assertSame('14:30:00', $result->createdAt->format('H:i:s'));
    }

    public function testDateTimeImmutableFromSerializedRepresentation(): void
    {
        $original = new DateTimeImmutable('2025-06-15T14:30:00', new DateTimeZone('Europe/Berlin'));

        $array = [
            'name' => 'test',
            'createdAt' => json_decode(json_encode($original), true),
        ];

        $result = $this->dataMapper()->array($array, TypeDateTimeImmutable::class);

        $this->assertInstanceOf(TypeDateTimeImmutable::class, $result);
        $this->assertSame('test', $result->name);
        $this->assertInstanceOf(DateTimeImmutable::class, $result->createdAt);
        $this->assertSame('2025-06-15', $result->createdAt->format('Y-m-d'));
        $this->assertSame('14:30:00', $result->createdAt->format('H:i:s'));
    }

    public function testMultipleDateTimeImmutableFromSerializedRepresentation(): void
    {
        $start = new DateTimeImmutable('2025-06-15T10:00:00', new DateTimeZone('Europe/Berlin'));
        $end = new DateTimeImmutable('2025-06-15T11:00:00', new DateTimeZone('Europe/Berlin'));

        $array = [
            'start' => json_decode(json_encode($start), true),
            'end' => json_decode(json_encode($end), true),
            'value' => 12.34,
        ];

        $result = $this->dataMapper()->array($array, TypeDateTimeImmutableNested::class);

        $this->assertInstanceOf(TypeDateTimeImmutableNested::class, $result);
        $this->assertInstanceOf(DateTimeImmutable::class, $result->start);
        $this->assertInstanceOf(DateTimeImmutable::class, $result->end);
        $this->assertSame('10:00:00', $result->start->format('H:i:s'));
        $this->assertSame('11:00:00', $result->end->format('H:i:s'));
        $this->assertSame(12.34, $result->value);
    }
}
