<?php

declare(strict_types=1);

namespace Integration;

use Integration\Objects\Types\TypeArray;
use Integration\Objects\Types\TypeBool;
use Integration\Objects\Types\TypeFloat;
use Integration\Objects\Types\TypeInt;
use Integration\Objects\Types\TypeNull;
use Integration\Objects\Types\TypeObject;
use Integration\Objects\Types\TypeObjectArray;
use Integration\Objects\Types\TypeString;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;

class NeonTypesTest extends TestCase
{
    public function dataMapper(): DataMapper
    {
        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        return new DataMapper($dataConfig);
    }

    public function testNull(): void
    {
        $file = __DIR__ . '/NeonFiles/TypeNull01.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeNull::class);

        $expected = new TypeNull(null);

        $this->assertInstanceOf(TypeNull::class, $return);
        $this->assertEquals($expected, $return);
        $this->assertNull($return->string);

        $file = __DIR__ . '/NeonFiles/TypeNull02.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeNull::class);

        $expected = new TypeNull(null);

        $this->assertInstanceOf(TypeNull::class, $return);
        $this->assertEquals($expected, $return);
        $this->assertNull($return->string);
    }

    public function testBoolClassic(): void
    {
        $file = __DIR__ . '/NeonFiles/TypeBool01.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeBool::class);

        $expected = new TypeBool(true, false);

        $this->assertInstanceOf(TypeBool::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testBoolInt(): void
    {
        $file = __DIR__ . '/NeonFiles/TypeBool02.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeBool::class);

        $expected = new TypeBool(true, false);

        $this->assertInstanceOf(TypeBool::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testBoolYesNo(): void
    {
        $file = __DIR__ . '/NeonFiles/TypeBool03.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeBool::class);

        $expected = new TypeBool(true, false);

        $this->assertInstanceOf(TypeBool::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testBoolOnOff(): void
    {
        $file = __DIR__ . '/NeonFiles/TypeBool04.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeBool::class);

        $expected = new TypeBool(true, false);

        $this->assertInstanceOf(TypeBool::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testIntClassic(): void
    {
        $file = __DIR__ . '/NeonFiles/TypeInt01.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeInt::class);

        $expected = new TypeInt(22);

        $this->assertInstanceOf(TypeInt::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testIntConvert(): void
    {
        $file = __DIR__ . '/NeonFiles/TypeInt02.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeInt::class);

        $expected = new TypeInt(33);

        $this->assertInstanceOf(TypeInt::class, $return);
        $this->assertEquals($expected, $return);

        $file = __DIR__ . '/NeonFiles/TypeInt03.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeInt::class);

        $expected = new TypeInt(44);

        $this->assertInstanceOf(TypeInt::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testFloatClassic(): void
    {
        $file = __DIR__ . '/NeonFiles/TypeFloat01.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeFloat::class);

        $expected = new TypeFloat(12.34);

        $this->assertInstanceOf(TypeFloat::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testFloatConvert(): void
    {
        $file = __DIR__ . '/NeonFiles/TypeFloat02.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeFloat::class);

        $expected = new TypeFloat(12.0);

        $this->assertInstanceOf(TypeFloat::class, $return);
        $this->assertEquals($expected, $return);

        $file = __DIR__ . '/NeonFiles/TypeFloat03.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeFloat::class);

        $expected = new TypeFloat(56.78);

        $this->assertInstanceOf(TypeFloat::class, $return);
        $this->assertEquals($expected, $return);

        $file = __DIR__ . '/NeonFiles/TypeFloat04.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeFloat::class);

        $expected = new TypeFloat(12.34);

        $this->assertInstanceOf(TypeFloat::class, $return);
        $this->assertEquals($expected, $return);

        $file = __DIR__ . '/NeonFiles/TypeFloat05.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeFloat::class);

        $expected = new TypeFloat(1234.56);

        $this->assertInstanceOf(TypeFloat::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testString(): void
    {
        $file = __DIR__ . '/NeonFiles/TypeString.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeString::class);

        $expected = new TypeString('Nostromo');

        $this->assertInstanceOf(TypeString::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testArray(): void
    {
        $file = __DIR__ . '/NeonFiles/TypeArray.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeArray::class);

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

    public function testObject(): void
    {
        $file = __DIR__ . '/NeonFiles/TypeObject.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeObject::class);

        $expected = new TypeObject(
            new TypeString('Nostromo'),
        );

        $this->assertInstanceOf(TypeObject::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testObjectArray(): void
    {
        $file = __DIR__ . '/NeonFiles/TypeObjectArray.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeObjectArray::class);

        $expected = new TypeObjectArray(
            [
                new TypeString('one'),
                new TypeString('two'),
            ],
        );

        $this->assertInstanceOf(TypeObjectArray::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testStringCrossTypes(): void
    {
        $file = __DIR__ . '/NeonFiles/TypeStringWithInt.neon';

        $return = $this->dataMapper()->neon(file_get_contents($file), TypeString::class);

        $expected = new TypeString('12345');

        $this->assertInstanceOf(TypeString::class, $return);
        $this->assertEquals($expected, $return);
    }
}
