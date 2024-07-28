<?php

declare(strict_types=1);

namespace Unit\Enum;

use DataMapper\Enum\DataTypeEnum;
use PHPUnit\Framework\TestCase;

class DataTypeEnumTest extends TestCase
{
    public function testDataTypeNull(): void
    {
        $dataType = DataTypeEnum::fromString(null);
        $this->assertInstanceOf(DataTypeEnum::class, $dataType);
        $this->assertSame(DataTypeEnum::NULL, $dataType);

        $dataType = DataTypeEnum::fromString('null');
        $this->assertInstanceOf(DataTypeEnum::class, $dataType);
        $this->assertSame(DataTypeEnum::NULL, $dataType);
    }

    public function testDataTypeBool(): void
    {
        $dataType = DataTypeEnum::fromString('bool');
        $this->assertInstanceOf(DataTypeEnum::class, $dataType);
        $this->assertSame(DataTypeEnum::BOOLEAN, $dataType);

        $dataType = DataTypeEnum::fromString('boolean');
        $this->assertInstanceOf(DataTypeEnum::class, $dataType);
        $this->assertSame(DataTypeEnum::BOOLEAN, $dataType);
    }

    public function testDataTypeInt(): void
    {
        $dataType = DataTypeEnum::fromString('int');
        $this->assertInstanceOf(DataTypeEnum::class, $dataType);
        $this->assertSame(DataTypeEnum::INTEGER, $dataType);

        $dataType = DataTypeEnum::fromString('integer');
        $this->assertInstanceOf(DataTypeEnum::class, $dataType);
        $this->assertSame(DataTypeEnum::INTEGER, $dataType);
    }

    public function testDataTypeFloat(): void
    {
        $dataType = DataTypeEnum::fromString('float');
        $this->assertInstanceOf(DataTypeEnum::class, $dataType);
        $this->assertSame(DataTypeEnum::FLOAT, $dataType);
    }

    public function testDataTypeArray(): void
    {
        $dataType = DataTypeEnum::fromString('array');
        $this->assertInstanceOf(DataTypeEnum::class, $dataType);
        $this->assertSame(DataTypeEnum::ARRAY, $dataType);
    }

    public function testDataTypeObject(): void
    {
        $dataType = DataTypeEnum::fromString('object');
        $this->assertInstanceOf(DataTypeEnum::class, $dataType);
        $this->assertSame(DataTypeEnum::OBJECT, $dataType);
    }

    public function testDataTypeInstance(): void
    {
        $dataType = DataTypeEnum::fromString('MockClasses\ItemConstructor');
        $this->assertIsString($dataType);
        $this->assertSame('MockClasses\ItemConstructor', $dataType);
    }

    public function testDataTypeInstanceArray(): void
    {
        $dataType = DataTypeEnum::fromString('MockClasses\ItemConstructor[]');
        $this->assertIsString($dataType);
        $this->assertSame('MockClasses\ItemConstructor[]', $dataType);
    }
}
