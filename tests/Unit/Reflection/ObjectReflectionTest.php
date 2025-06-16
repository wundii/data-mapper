<?php

declare(strict_types=1);

namespace Unit\Reflection;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Enum\DataTypeEnum;
use Wundii\DataMapper\Enum\VisibilityEnum;
use Wundii\DataMapper\Reflection\ObjectReflection;
use Wundii\DataMapper\Reflection\PropertyReflection;

class ObjectReflectionTest extends TestCase
{
    public function objectEmpty(): ObjectReflection
    {
        return new ObjectReflection([], [], [], [], []);
    }

    public function objectComplex(): ObjectReflection
    {
        return new ObjectReflection(
            [
                new PropertyReflection('nameProperty', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
                new PropertyReflection('dataProperty', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
                new PropertyReflection('itemProperty', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
            ],
            [
                new PropertyReflection('nameConstructor', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
                new PropertyReflection('dataConstructor', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
                new PropertyReflection('itemConstructor', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
            ],
            [
                new PropertyReflection('nameGetter', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
                new PropertyReflection('dataGetter', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
                new PropertyReflection('itemGetter', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
            ],
            [
                new PropertyReflection('nameSetter', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
                new PropertyReflection('dataSetter', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
                new PropertyReflection('itemSetter', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
            ],
            [
                new PropertyReflection('nameAttribute', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
                new PropertyReflection('dataAttribute', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
                new PropertyReflection('itemAttribute', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
            ]
        );
    }

    public function testFindEmpty(): void
    {
        $object = $this->objectEmpty();

        $this->assertEquals([], $object->getProperties());
        $this->assertEquals([], $object->getConstructor());
        $this->assertEquals([], $object->getSetters());

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

        $expectedProperties = [
            new PropertyReflection('nameProperty', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
            new PropertyReflection('dataProperty', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
            new PropertyReflection('itemProperty', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
        ];
        $this->assertEquals($expectedProperties, $object->getProperties());

        $expectedConstructors = [
            new PropertyReflection('nameConstructor', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
            new PropertyReflection('dataConstructor', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
            new PropertyReflection('itemConstructor', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
        ];
        $this->assertEquals($expectedConstructors, $object->getConstructor());

        $expectedGetters = [
            new PropertyReflection('nameGetter', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
            new PropertyReflection('dataGetter', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
            new PropertyReflection('itemGetter', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
        ];
        $this->assertEquals($expectedGetters, $object->getGetters());

        $expectedSetters = [
            new PropertyReflection('nameSetter', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
            new PropertyReflection('dataSetter', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
            new PropertyReflection('itemSetter', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
        ];
        $this->assertEquals($expectedSetters, $object->getSetters());

        $expectedAttribute = [
            new PropertyReflection('nameAttribute', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
            new PropertyReflection('dataAttribute', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
            new PropertyReflection('itemAttribute', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
        ];
        $this->assertEquals($expectedAttribute, $object->getAttributes());

        $expectedAvailableData = [
            'nameProperty' => new PropertyReflection('nameProperty', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
            'nameGetter' => new PropertyReflection('nameGetter', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
            'nameAttribute' => new PropertyReflection('nameAttribute', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
        ];
        $this->assertEquals($expectedAvailableData, $object->availableData());

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
