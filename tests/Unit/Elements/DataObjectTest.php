<?php

declare(strict_types=1);

namespace Unit\Elements;

use DateTimeInterface;
use InvalidArgumentException;
use MockClasses\RootProperties;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Elements\DataObject;
use Wundii\DataMapper\Elements\DataString;

class DataObjectTest extends TestCase
{
    public function testCreateInstanceException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('object MockClasses\Fail does not exist');

        $array = [
            new DataString('value1'),
        ];

        new DataObject('MockClasses\Fail', $array, 'string');
    }

    public function testCreateInstanceWithClassString(): void
    {
        $array = [
            new DataString('value1'),
        ];

        $dataObject = new DataObject('MockClasses\RootConstructor', $array, 'string');
        $this->assertInstanceOf(DataObject::class, $dataObject);
    }

    public function testCreateInstanceWithInterfaceString(): void
    {
        $array = [
            new DataString('value1'),
        ];

        $dataObject = new DataObject(DateTimeInterface::class, $array, 'string');
        $this->assertInstanceOf(DataObject::class, $dataObject);
    }

    public function testInstanceToStringWithClassString(): void
    {
        $array = [
            new DataString('value1'),
        ];

        $dataObject = new DataObject('MockClasses\RootConstructor', $array, 'string');
        $this->assertSame('MockClasses\RootConstructor', (string) $dataObject);
    }

    public function testInstanceToStringWithObject(): void
    {
        $array = [
            new DataString('value1'),
        ];

        $dataObject = new DataObject(new RootProperties(), $array, 'string');
        $this->assertSame('MockClasses\RootProperties', (string) $dataObject);
    }
}
