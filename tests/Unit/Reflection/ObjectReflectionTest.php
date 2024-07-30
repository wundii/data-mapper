<?php

declare(strict_types=1);

namespace Unit\Reflection;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Enum\DataTypeEnum;
use Wundii\DataMapper\Reflection\ObjectReflection;
use Wundii\DataMapper\Reflection\PropertyReflection;

class ObjectReflectionTest extends TestCase
{
    public function objectEmpty(): ObjectReflection
    {
        return new ObjectReflection([], [], []);
    }

    public function objectComplex(): ObjectReflection
    {
        return new ObjectReflection(
            [
                new PropertyReflection('nameProperty', DataTypeEnum::STRING, 'target1', false, true),
                new PropertyReflection('dataProperty', DataTypeEnum::ARRAY, 'target2', true, false),
                new PropertyReflection('itemProperty', 'MockClasses\ItemConstructor', 'target3', true, false),
            ],
            [
                new PropertyReflection('nameConstructor', DataTypeEnum::STRING, 'target1', false, true),
                new PropertyReflection('dataConstructor', DataTypeEnum::ARRAY, 'target2', true, false),
                new PropertyReflection('itemConstructor', 'MockClasses\ItemConstructor', 'target3', true, false),
            ],
            [
                new PropertyReflection('nameSetter', DataTypeEnum::STRING, 'target1', false, true),
                new PropertyReflection('dataSetter', DataTypeEnum::ARRAY, 'target2', true, false),
                new PropertyReflection('itemSetter', 'MockClasses\ItemConstructor', 'target3', true, false),
            ],
        );
    }

    public function testFindEmpty(): void
    {
        $object = $this->objectEmpty();

        $this->assertNull($object->find(ApproachEnum::PROPERTY, 'nameProperty'));
        $this->assertNull($object->find(ApproachEnum::PROPERTY, 'NAMEPROPERTY'));
        $this->assertNull($object->find(ApproachEnum::CONSTRUCTOR, 'dataConstructor'));
        $this->assertNull($object->find(ApproachEnum::CONSTRUCTOR, 'DATACONSTRUCTOR'));
        $this->assertNull($object->find(ApproachEnum::SETTER, 'itemSetter'));
        $this->assertNull($object->find(ApproachEnum::SETTER, 'ITEMSETTER'));
    }

    public function testFind(): void
    {
        $object = $this->objectComplex();

        $this->assertInstanceOf(PropertyReflection::class, $object->find(ApproachEnum::PROPERTY, 'nameProperty'));
        $this->assertSame('nameProperty', $object->find(ApproachEnum::PROPERTY, 'nameProperty')?->getName());
        $this->assertSame('nameProperty', $object->find(ApproachEnum::PROPERTY, 'NAMEPROPERTY')?->getName());
        $this->assertInstanceOf(PropertyReflection::class, $object->find(ApproachEnum::CONSTRUCTOR, 'dataConstructor'));
        $this->assertSame('dataConstructor', $object->find(ApproachEnum::CONSTRUCTOR, 'dataConstructor')?->getName());
        $this->assertSame('dataConstructor', $object->find(ApproachEnum::CONSTRUCTOR, 'DATACONSTRUCTOR')?->getName());
        $this->assertInstanceOf(PropertyReflection::class, $object->find(ApproachEnum::SETTER, 'itemSetter'));
        $this->assertSame('itemSetter', $object->find(ApproachEnum::SETTER, 'itemSetter')?->getName());
        $this->assertSame('itemSetter', $object->find(ApproachEnum::SETTER, 'ITEMSETTER')?->getName());
    }
}
