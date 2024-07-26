<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration;

use DataMapper\DataConfig;
use DataMapper\DataMapper;
use DataMapper\Tests\Integration\Objects\Types\TypeArray;
use DataMapper\Tests\Integration\Objects\Types\TypeBool;
use DataMapper\Tests\Integration\Objects\Types\TypeFloat;
use DataMapper\Tests\Integration\Objects\Types\TypeInt;
use DataMapper\Tests\Integration\Objects\Types\TypeNull;
use DataMapper\Tests\Integration\Objects\Types\TypeString;
use PHPUnit\Framework\TestCase;

class XmlTypesTest extends TestCase
{
    public function testNull(): void
    {
        $file = __DIR__ . '/XmlFiles/TypeNull.xml';

        $dataMapper = new DataMapper(new DataConfig());
        $return = $dataMapper->xml(file_get_contents($file), TypeNull::class);

        $expected = new TypeNull(null);

        $this->assertInstanceOf(TypeNull::class, $return);
        $this->assertEquals($expected, $return);
        $this->assertNull($return->string);
    }

    public function testBoolClassic(): void
    {
        $file = __DIR__ . '/XmlFiles/TypeBool01.xml';

        $dataMapper = new DataMapper(new DataConfig());
        $return = $dataMapper->xml(file_get_contents($file), TypeBool::class);

        $expected = new TypeBool(true, false);

        $this->assertInstanceOf(TypeBool::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testBoolInt(): void
    {
        $file = __DIR__ . '/XmlFiles/TypeBool02.xml';

        $dataMapper = new DataMapper(new DataConfig());
        $return = $dataMapper->xml(file_get_contents($file), TypeBool::class);

        $expected = new TypeBool(true, false);

        $this->assertInstanceOf(TypeBool::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testBoolYesNo(): void
    {
        $file = __DIR__ . '/XmlFiles/TypeBool03.xml';

        $dataMapper = new DataMapper(new DataConfig());
        $return = $dataMapper->xml(file_get_contents($file), TypeBool::class);

        $expected = new TypeBool(true, false);

        $this->assertInstanceOf(TypeBool::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testBoolOnOff(): void
    {
        $file = __DIR__ . '/XmlFiles/TypeBool04.xml';

        $dataMapper = new DataMapper(new DataConfig());
        $return = $dataMapper->xml(file_get_contents($file), TypeBool::class);

        $expected = new TypeBool(true, false);

        $this->assertInstanceOf(TypeBool::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testIntClassic(): void
    {
        $file = __DIR__ . '/XmlFiles/TypeInt01.xml';

        $dataMapper = new DataMapper(new DataConfig());
        $return = $dataMapper->xml(file_get_contents($file), TypeInt::class);

        $expected = new TypeInt(22);

        $this->assertInstanceOf(TypeInt::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testIntConvert(): void
    {
        $file = __DIR__ . '/XmlFiles/TypeInt02.xml';

        $dataMapper = new DataMapper(new DataConfig());
        $return = $dataMapper->xml(file_get_contents($file), TypeInt::class);

        $expected = new TypeInt(33);

        $this->assertInstanceOf(TypeInt::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testFloatClassic(): void
    {
        $file = __DIR__ . '/XmlFiles/TypeFloat01.xml';

        $dataMapper = new DataMapper(new DataConfig());
        $return = $dataMapper->xml(file_get_contents($file), TypeFloat::class);

        $expected = new TypeFloat(12.34);

        $this->assertInstanceOf(TypeFloat::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testFloatConvert(): void
    {
        $file = __DIR__ . '/XmlFiles/TypeFloat02.xml';

        $dataMapper = new DataMapper(new DataConfig());
        $return = $dataMapper->xml(file_get_contents($file), TypeFloat::class);

        $expected = new TypeFloat(12.0);

        $this->assertInstanceOf(TypeFloat::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testString(): void
    {
        $file = __DIR__ . '/XmlFiles/TypeString.xml';

        $dataMapper = new DataMapper(new DataConfig());
        $return = $dataMapper->xml(file_get_contents($file), TypeString::class);

        $expected = new TypeString('Nostromo');

        $this->assertInstanceOf(TypeString::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testArray(): void
    {
        $file = __DIR__ . '/XmlFiles/TypeArray.xml';

        $dataMapper = new DataMapper(new DataConfig());
        $return = $dataMapper->xml(file_get_contents($file), TypeArray::class);

        $expected = new TypeArray(
            [
                'one',
                'two',
            ],
            [
                1,
                2,
            ],
            [
                3.3,
                4.4,
            ],
        );

        $this->assertInstanceOf(TypeArray::class, $return);
        $this->assertEquals($expected, $return);
    }
}
