<?php

declare(strict_types=1);

namespace Unit\Dto\Type;

use DateTimeInterface;
use InvalidArgumentException;
use MockClasses\RootProperties;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Dto\Type\ObjectDto;
use Wundii\DataMapper\Dto\Type\StringDto;

class ObjectDtoTest extends TestCase
{
    public function testCreateInstanceException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('object MockClasses\Fail does not exist');

        $array = [
            new StringDto('value1'),
        ];

        new ObjectDto('MockClasses\Fail', $array, 'string');
    }

    public function testCreateInstanceWithClassString(): void
    {
        $array = [
            new StringDto('value1'),
        ];

        $objectDto = new ObjectDto('MockClasses\RootConstructor', $array, 'string');
        $this->assertInstanceOf(ObjectDto::class, $objectDto);
    }

    public function testCreateInstanceWithInterfaceString(): void
    {
        $array = [
            new StringDto('value1'),
        ];

        $objectDto = new ObjectDto(DateTimeInterface::class, $array, 'string');
        $this->assertInstanceOf(ObjectDto::class, $objectDto);
    }

    public function testInstanceToStringWithClassString(): void
    {
        $array = [
            new StringDto('value1'),
        ];

        $objectDto = new ObjectDto('MockClasses\RootConstructor', $array, 'string');
        $this->assertSame('MockClasses\RootConstructor', (string) $objectDto);
    }

    public function testInstanceToStringWithObject(): void
    {
        $array = [
            new StringDto('value1'),
        ];

        $objectDto = new ObjectDto(new RootProperties(), $array, 'string');
        $this->assertSame('MockClasses\RootProperties', (string) $objectDto);
    }
}
