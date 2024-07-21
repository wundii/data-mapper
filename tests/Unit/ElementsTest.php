<?php

declare(strict_types=1);

namespace DataMapper\Tests\Unit;

use DataMapper\Elements\DataArray;
use DataMapper\Elements\DataBool;
use DataMapper\Elements\DataFloat;
use DataMapper\Elements\DataInt;
use DataMapper\Elements\DataNull;
use DataMapper\Elements\DataObject;
use DataMapper\Elements\DataString;
use DataMapper\Interface\ArrayElementInterface;
use DataMapper\Interface\DataElementInterface;
use DataMapper\Interface\ObjectElementInterface;
use DataMapper\Tests\MockClasses\RootClassConstructor;
use DataMapper\Tests\MockClasses\RootClassInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ElementsTest extends TestCase
{
    public function testElementNull(): void
    {
        $element = new DataNull();
        $this->assertInstanceOf(DataElementInterface::class, $element);
        $this->assertNull($element->getValue());
        $this->assertNull($element->getDestination());

        $element = new DataNull('destination');
        $this->assertInstanceOf(DataElementInterface::class, $element);
        $this->assertNull($element->getValue());
        $this->assertSame('destination', $element->getDestination());
    }

    public function testElementBool(): void
    {
        $element = new DataBool(false);
        $this->assertInstanceOf(DataElementInterface::class, $element);
        $this->assertFalse($element->getValue());
        $this->assertNull($element->getDestination());

        $element = new DataBool(true, 'destination');
        $this->assertInstanceOf(DataElementInterface::class, $element);
        $this->assertTrue($element->getValue());
        $this->assertSame('destination', $element->getDestination());
    }

    public function testElementInt(): void
    {
        $element = new DataInt(1);
        $this->assertInstanceOf(DataElementInterface::class, $element);
        $this->assertSame(1, $element->getValue());
        $this->assertNull($element->getDestination());

        $element = new DataInt(2, 'destination');
        $this->assertInstanceOf(DataElementInterface::class, $element);
        $this->assertSame(2, $element->getValue());
        $this->assertSame('destination', $element->getDestination());
    }

    public function testElementFloat(): void
    {
        $element = new DataFloat(1.1);
        $this->assertInstanceOf(DataElementInterface::class, $element);
        $this->assertSame(1.1, $element->getValue());
        $this->assertNull($element->getDestination());

        $element = new DataFloat(2.2, 'destination');
        $this->assertInstanceOf(DataElementInterface::class, $element);
        $this->assertSame(2.2, $element->getValue());
        $this->assertSame('destination', $element->getDestination());
    }

    public function testElementString(): void
    {
        $element = new DataString('unittest');
        $this->assertInstanceOf(DataElementInterface::class, $element);
        $this->assertSame('unittest', $element->getValue());
        $this->assertNull($element->getDestination());

        $element = new DataString('unittest', 'destination');
        $this->assertInstanceOf(DataElementInterface::class, $element);
        $this->assertSame('unittest', $element->getValue());
        $this->assertSame('destination', $element->getDestination());
    }

    public function testElementArray(): void
    {
        $array = [
            new DataString('unittest'),
            new DataInt(1),
        ];
        $element = new DataArray($array);
        $this->assertInstanceOf(DataElementInterface::class, $element);
        $this->assertInstanceOf(ArrayElementInterface::class, $element);
        $this->assertSame($array, $element->getValue());
        $this->assertNull($element->getDestination());

        $element = new DataArray($array, 'destination');
        $this->assertInstanceOf(DataElementInterface::class, $element);
        $this->assertSame('destination', $element->getDestination());
    }

    public function testElementObjectException(): void
    {
        $array = [
            new DataString('unittest'),
            new DataInt(1),
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('object fail does not exist');

        new DataObject('fail', $array);
    }

    public function testElementObject(): void
    {
        $array = [
            new DataString('unittest'),
            new DataInt(1),
        ];
        $element = new DataObject(RootClassConstructor::class, $array);
        $this->assertInstanceOf(DataElementInterface::class, $element);
        $this->assertInstanceOf(ObjectElementInterface::class, $element);
        $this->assertSame($array, $element->getValue());
        $this->assertNull($element->getDestination());
        $this->assertSame(RootClassConstructor::class, $element->getObjectName());

        $element = new DataObject(RootClassInterface::class, $array, 'destination');
        $this->assertInstanceOf(DataElementInterface::class, $element);
        $this->assertSame('destination', $element->getDestination());
    }
}
