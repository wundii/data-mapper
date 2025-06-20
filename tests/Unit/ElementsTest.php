<?php

declare(strict_types=1);

namespace Unit;

use InvalidArgumentException;
use MockClasses\RootConstructor;
use MockClasses\RootInterface;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Dto\Type\ArrayDto;
use Wundii\DataMapper\Dto\Type\BoolDto;
use Wundii\DataMapper\Dto\Type\FloatDto;
use Wundii\DataMapper\Dto\Type\IntDto;
use Wundii\DataMapper\Dto\Type\NullDto;
use Wundii\DataMapper\Dto\Type\ObjectDto;
use Wundii\DataMapper\Dto\Type\StringDto;
use Wundii\DataMapper\Interface\ArrayDtoInterface;
use Wundii\DataMapper\Interface\ObjectDtoInterface;
use Wundii\DataMapper\Interface\TypeDtoInterface;

class ElementsTest extends TestCase
{
    public function testElementNull(): void
    {
        $typeDto = new NullDto();
        $this->assertInstanceOf(TypeDtoInterface::class, $typeDto);
        $this->assertNull($typeDto->getValue());
        $this->assertNull($typeDto->getDestination());

        $typeDto = new NullDto('destination');
        $this->assertInstanceOf(TypeDtoInterface::class, $typeDto);
        $this->assertNull($typeDto->getValue());
        $this->assertSame('destination', $typeDto->getDestination());
    }

    public function testElementBool(): void
    {
        $typeDto = new BoolDto(false);
        $this->assertInstanceOf(TypeDtoInterface::class, $typeDto);
        $this->assertFalse($typeDto->getValue());
        $this->assertNull($typeDto->getDestination());

        $typeDto = new BoolDto(true, 'destination');
        $this->assertInstanceOf(TypeDtoInterface::class, $typeDto);
        $this->assertTrue($typeDto->getValue());
        $this->assertSame('destination', $typeDto->getDestination());
    }

    public function testElementInt(): void
    {
        $typeDto = new IntDto(1);
        $this->assertInstanceOf(TypeDtoInterface::class, $typeDto);
        $this->assertSame(1, $typeDto->getValue());
        $this->assertNull($typeDto->getDestination());

        $typeDto = new IntDto(2, 'destination');
        $this->assertInstanceOf(TypeDtoInterface::class, $typeDto);
        $this->assertSame(2, $typeDto->getValue());
        $this->assertSame('destination', $typeDto->getDestination());
    }

    public function testElementFloat(): void
    {
        $typeDto = new FloatDto(1.1);
        $this->assertInstanceOf(TypeDtoInterface::class, $typeDto);
        $this->assertSame(1.1, $typeDto->getValue());
        $this->assertNull($typeDto->getDestination());

        $typeDto = new FloatDto(2.2, 'destination');
        $this->assertInstanceOf(TypeDtoInterface::class, $typeDto);
        $this->assertSame(2.2, $typeDto->getValue());
        $this->assertSame('destination', $typeDto->getDestination());
    }

    public function testElementString(): void
    {
        $typeDto = new StringDto('unittest');
        $this->assertInstanceOf(TypeDtoInterface::class, $typeDto);
        $this->assertSame('unittest', $typeDto->getValue());
        $this->assertNull($typeDto->getDestination());

        $typeDto = new StringDto('unittest', 'destination');
        $this->assertInstanceOf(TypeDtoInterface::class, $typeDto);
        $this->assertSame('unittest', $typeDto->getValue());
        $this->assertSame('destination', $typeDto->getDestination());
    }

    public function testElementArray(): void
    {
        $array = [
            new StringDto('unittest'),
            new IntDto(1),
        ];
        $typeDto = new ArrayDto($array);
        $this->assertInstanceOf(TypeDtoInterface::class, $typeDto);
        $this->assertInstanceOf(ArrayDtoInterface::class, $typeDto);
        $this->assertSame($array, $typeDto->getValue());
        $this->assertNull($typeDto->getDestination());

        $typeDto = new ArrayDto($array, 'destination');
        $this->assertInstanceOf(TypeDtoInterface::class, $typeDto);
        $this->assertSame('destination', $typeDto->getDestination());
    }

    public function testElementObjectException(): void
    {
        $array = [
            new StringDto('unittest'),
            new IntDto(1),
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('object fail does not exist');

        new ObjectDto('fail', $array);
    }

    public function testElementObject(): void
    {
        $array = [
            new StringDto('unittest'),
            new IntDto(1),
        ];
        $typeDto = new ObjectDto(RootConstructor::class, $array);
        $this->assertInstanceOf(TypeDtoInterface::class, $typeDto);
        $this->assertInstanceOf(ObjectDtoInterface::class, $typeDto);
        $this->assertSame($array, $typeDto->getValue());
        $this->assertNull($typeDto->getDestination());
        $this->assertSame(RootConstructor::class, $typeDto->getObject());

        $typeDto = new ObjectDto(RootInterface::class, $array, 'destination');
        $this->assertInstanceOf(TypeDtoInterface::class, $typeDto);
        $this->assertSame('destination', $typeDto->getDestination());
    }
}
