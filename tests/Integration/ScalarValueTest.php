<?php

declare(strict_types=1);

namespace Integration;

use Integration\Objects\Types\TypeFloat;
use Integration\Objects\Types\TypeInt;
use Integration\Objects\Types\TypeString;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Exception\DataMapperException;

/**
 * Source data that does not fit the target type used to end up as the string
 * "Array" or as a raw TypeError from the value dtos.
 */
class ScalarValueTest extends TestCase
{
    public function dataMapper(): DataMapper
    {
        return new DataMapper(new DataConfig(ApproachEnum::PROPERTY));
    }

    public function testArrayForAStringTargetIsReported(): void
    {
        $this->expectException(DataMapperException::class);
        $this->expectExceptionMessage('Expected value of type string for the target string, got array');

        $this->dataMapper()
            ->json('{"string": {"first": "Ada"}}', TypeString::class);
    }

    public function testArrayForAnIntegerTargetIsReported(): void
    {
        $this->expectException(DataMapperException::class);
        $this->expectExceptionMessage('Expected value of type integer for the target int, got array');

        $this->dataMapper()
            ->json('{"int": {"years": 36}}', TypeInt::class);
    }

    public function testValidScalarValuesStillMap(): void
    {
        $result = $this->dataMapper()
            ->json('{"string": "Ada"}', TypeString::class);

        $this->assertInstanceOf(TypeString::class, $result);
    }

    /**
     * IntDto does not accept a bool, which used to end in a raw TypeError.
     */
    public function testBoolForAnIntegerTargetIsReported(): void
    {
        $this->expectException(DataMapperException::class);
        $this->expectExceptionMessage('Expected value of type integer for the target int, got bool');

        $this->dataMapper()
            ->json('{"int": true}', TypeInt::class);
    }

    public function testBoolForAFloatTargetIsReported(): void
    {
        $this->expectException(DataMapperException::class);
        $this->expectExceptionMessage('Expected value of type float for the target float, got bool');

        $this->dataMapper()
            ->json('{"float": true}', TypeFloat::class);
    }

    /**
     * int to float is a widening conversion and has to keep working.
     */
    public function testIntegerForAFloatTargetStillMaps(): void
    {
        $result = $this->dataMapper()
            ->json('{"float": 7}', TypeFloat::class);

        $this->assertInstanceOf(TypeFloat::class, $result);
    }
}
