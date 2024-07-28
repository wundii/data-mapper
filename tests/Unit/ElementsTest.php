<?php

declare(strict_types=1);

namespace Unit;

use DataMapper\Elements\DataArray;
use DataMapper\Elements\DataBool;
use DataMapper\Elements\DataFloat;
use DataMapper\Elements\DataInt;
use DataMapper\Elements\DataNull;
use DataMapper\Elements\DataObject;
use DataMapper\Elements\DataString;
use DataMapper\Interface\ElementArrayInterface;
use DataMapper\Interface\ElementDataInterface;
use DataMapper\Interface\ElementObjectInterface;
use DataMapper\Tests\MockClasses\RootConstructor;
use DataMapper\Tests\MockClasses\RootInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ElementsTest extends TestCase
{
    public function testElementNull(): void
    {
        $element = new DataNull();
        $this->assertInstanceOf(ElementDataInterface::class, $element);
        $this->assertNull($element->getValue());
        $this->assertNull($element->getDestination());

        $element = new DataNull('destination');
        $this->assertInstanceOf(ElementDataInterface::class, $element);
        $this->assertNull($element->getValue());
        $this->assertSame('destination', $element->getDestination());
    }

    public function testElementBool(): void
    {
        $element = new DataBool(false);
        $this->assertInstanceOf(ElementDataInterface::class, $element);
        $this->assertFalse($element->getValue());
        $this->assertNull($element->getDestination());

        $element = new DataBool(true, 'destination');
        $this->assertInstanceOf(ElementDataInterface::class, $element);
        $this->assertTrue($element->getValue());
        $this->assertSame('destination', $element->getDestination());
    }

    public function testElementInt(): void
    {
        $element = new DataInt(1);
        $this->assertInstanceOf(ElementDataInterface::class, $element);
        $this->assertSame(1, $element->getValue());
        $this->assertNull($element->getDestination());

        $element = new DataInt(2, 'destination');
        $this->assertInstanceOf(ElementDataInterface::class, $element);
        $this->assertSame(2, $element->getValue());
        $this->assertSame('destination', $element->getDestination());
    }

    public function testElementFloat(): void
    {
        $element = new DataFloat(1.1);
        $this->assertInstanceOf(ElementDataInterface::class, $element);
        $this->assertSame(1.1, $element->getValue());
        $this->assertNull($element->getDestination());

        $element = new DataFloat(2.2, 'destination');
        $this->assertInstanceOf(ElementDataInterface::class, $element);
        $this->assertSame(2.2, $element->getValue());
        $this->assertSame('destination', $element->getDestination());
    }

    public function testElementString(): void
    {
        $element = new DataString('unittest');
        $this->assertInstanceOf(ElementDataInterface::class, $element);
        $this->assertSame('unittest', $element->getValue());
        $this->assertNull($element->getDestination());

        $element = new DataString('unittest', 'destination');
        $this->assertInstanceOf(ElementDataInterface::class, $element);
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
        $this->assertInstanceOf(ElementDataInterface::class, $element);
        $this->assertInstanceOf(ElementArrayInterface::class, $element);
        $this->assertSame($array, $element->getValue());
        $this->assertNull($element->getDestination());

        $element = new DataArray($array, 'destination');
        $this->assertInstanceOf(ElementDataInterface::class, $element);
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
        $element = new DataObject(RootConstructor::class, $array);
        $this->assertInstanceOf(ElementDataInterface::class, $element);
        $this->assertInstanceOf(ElementObjectInterface::class, $element);
        $this->assertSame($array, $element->getValue());
        $this->assertNull($element->getDestination());
        $this->assertSame(RootConstructor::class, $element->getObject());

        $element = new DataObject(RootInterface::class, $array, 'destination');
        $this->assertInstanceOf(ElementDataInterface::class, $element);
        $this->assertSame('destination', $element->getDestination());
    }
}
