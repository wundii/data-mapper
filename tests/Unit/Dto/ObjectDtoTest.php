<?php

declare(strict_types=1);

namespace Unit\Dto;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Dto\ObjectPropertyDto;
use Wundii\DataMapper\Dto\PropertyDto;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Enum\ClassElementTypeEnum;
use Wundii\DataMapper\Enum\DataTypeEnum;

class ObjectDtoTest extends TestCase
{
    public function objectEmpty(): ObjectPropertyDto
    {
        return new ObjectPropertyDto([], [], [], [], []);
    }

    public function objectComplex(): ObjectPropertyDto
    {
        return new ObjectPropertyDto(
            [
                new PropertyDto(ClassElementTypeEnum::PROPERTY, 'nameProperty', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
                new PropertyDto(ClassElementTypeEnum::PROPERTY, 'dataProperty', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
                new PropertyDto(ClassElementTypeEnum::PROPERTY, 'itemProperty', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
            ],
            [
                new PropertyDto(ClassElementTypeEnum::CONSTRUCTOR, 'nameConstructor', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
                new PropertyDto(ClassElementTypeEnum::CONSTRUCTOR, 'dataConstructor', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
                new PropertyDto(ClassElementTypeEnum::CONSTRUCTOR, 'itemConstructor', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
            ],
            [
                new PropertyDto(ClassElementTypeEnum::GETTER, 'nameGetter', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
                new PropertyDto(ClassElementTypeEnum::GETTER, 'dataGetter', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
                new PropertyDto(ClassElementTypeEnum::GETTER, 'itemGetter', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
            ],
            [
                new PropertyDto(ClassElementTypeEnum::SETTER, 'nameSetter', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
                new PropertyDto(ClassElementTypeEnum::SETTER, 'dataSetter', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
                new PropertyDto(ClassElementTypeEnum::SETTER, 'itemSetter', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
            ],
            [
                new PropertyDto(ClassElementTypeEnum::ATTRIBUTE_SOURCE, 'nameAttributeSource', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
                new PropertyDto(ClassElementTypeEnum::ATTRIBUTE_SOURCE, 'dataAttributeSource', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
                new PropertyDto(ClassElementTypeEnum::ATTRIBUTE_SOURCE, 'itemAttributeSource', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
                new PropertyDto(ClassElementTypeEnum::ATTRIBUTE_TARGET, 'nameAttributeTarget', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
                new PropertyDto(ClassElementTypeEnum::ATTRIBUTE_TARGET, 'dataAttributeTarget', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
                new PropertyDto(ClassElementTypeEnum::ATTRIBUTE_TARGET, 'itemAttributeTarget', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
            ]
        );
    }

    public function testFindEmpty(): void
    {
        $object = $this->objectEmpty();

        $this->assertEquals([], $object->getProperties());
        $this->assertEquals([], $object->getConstructor());
        $this->assertEquals([], $object->getSetters());

        $this->assertNull($object->findPropertyDto(ApproachEnum::PROPERTY, 'nameProperty'));
        $this->assertNull($object->findPropertyDto(ApproachEnum::PROPERTY, 'NAMEPROPERTY'));
        $this->assertNull($object->findPropertyDto(ApproachEnum::CONSTRUCTOR, 'dataConstructor'));
        $this->assertNull($object->findPropertyDto(ApproachEnum::CONSTRUCTOR, 'DATACONSTRUCTOR'));
        $this->assertNull($object->findPropertyDto(ApproachEnum::SETTER, 'itemSetter'));
        $this->assertNull($object->findPropertyDto(ApproachEnum::SETTER, 'ITEMSETTER'));
    }

    public function testFind(): void
    {
        $object = $this->objectComplex();

        $expectedProperties = [
            new PropertyDto(ClassElementTypeEnum::PROPERTY, 'nameProperty', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
            new PropertyDto(ClassElementTypeEnum::PROPERTY, 'dataProperty', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
            new PropertyDto(ClassElementTypeEnum::PROPERTY, 'itemProperty', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
        ];
        $this->assertEquals($expectedProperties, $object->getProperties());

        $expectedConstructors = [
            new PropertyDto(ClassElementTypeEnum::CONSTRUCTOR, 'nameConstructor', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
            new PropertyDto(ClassElementTypeEnum::CONSTRUCTOR, 'dataConstructor', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
            new PropertyDto(ClassElementTypeEnum::CONSTRUCTOR, 'itemConstructor', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
        ];
        $this->assertEquals($expectedConstructors, $object->getConstructor());

        $expectedGetters = [
            new PropertyDto(ClassElementTypeEnum::GETTER, 'nameGetter', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
            new PropertyDto(ClassElementTypeEnum::GETTER, 'dataGetter', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
            new PropertyDto(ClassElementTypeEnum::GETTER, 'itemGetter', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
        ];
        $this->assertEquals($expectedGetters, $object->getGetters());

        $expectedSetters = [
            new PropertyDto(ClassElementTypeEnum::SETTER, 'nameSetter', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
            new PropertyDto(ClassElementTypeEnum::SETTER, 'dataSetter', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
            new PropertyDto(ClassElementTypeEnum::SETTER, 'itemSetter', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
        ];
        $this->assertEquals($expectedSetters, $object->getSetters());

        $expectedAttribute = [
            new PropertyDto(ClassElementTypeEnum::ATTRIBUTE_SOURCE, 'nameAttributeSource', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
            new PropertyDto(ClassElementTypeEnum::ATTRIBUTE_SOURCE, 'dataAttributeSource', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
            new PropertyDto(ClassElementTypeEnum::ATTRIBUTE_SOURCE, 'itemAttributeSource', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
            new PropertyDto(ClassElementTypeEnum::ATTRIBUTE_TARGET, 'nameAttributeTarget', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
            new PropertyDto(ClassElementTypeEnum::ATTRIBUTE_TARGET, 'dataAttributeTarget', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
            new PropertyDto(ClassElementTypeEnum::ATTRIBUTE_TARGET, 'itemAttributeTarget', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
        ];
        $this->assertEquals($expectedAttribute, $object->getAttributes());

        $expectedAvailableData = [
            'nameProperty' => new PropertyDto(ClassElementTypeEnum::PROPERTY, 'nameProperty', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
            'nameGetter' => new PropertyDto(ClassElementTypeEnum::GETTER, 'nameGetter', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
            'nameAttributeSource' => new PropertyDto(ClassElementTypeEnum::ATTRIBUTE_SOURCE, 'nameAttributeSource', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
        ];
        $this->assertEquals($expectedAvailableData, $object->availableData());

        $expectedAttributeTargetType = new PropertyDto(ClassElementTypeEnum::ATTRIBUTE_TARGET, 'dataAttributeTarget', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED);
        $this->assertEquals($expectedAttributeTargetType, $object->findAttributeTargetPropertyDto('dataAttributeTarget'));

        $this->assertInstanceOf(PropertyDto::class, $object->findPropertyDto(ApproachEnum::PROPERTY, 'nameProperty'));
        $this->assertSame('nameProperty', $object->findPropertyDto(ApproachEnum::PROPERTY, 'nameProperty')?->getName());
        $this->assertSame('nameProperty', $object->findPropertyDto(ApproachEnum::PROPERTY, 'NAMEPROPERTY')?->getName());
        $this->assertInstanceOf(PropertyDto::class, $object->findPropertyDto(ApproachEnum::CONSTRUCTOR, 'dataConstructor'));
        $this->assertSame('dataConstructor', $object->findPropertyDto(ApproachEnum::CONSTRUCTOR, 'dataConstructor')?->getName());
        $this->assertSame('dataConstructor', $object->findPropertyDto(ApproachEnum::CONSTRUCTOR, 'DATACONSTRUCTOR')?->getName());
        $this->assertInstanceOf(PropertyDto::class, $object->findPropertyDto(ApproachEnum::SETTER, 'itemSetter'));
        $this->assertSame('itemSetter', $object->findPropertyDto(ApproachEnum::SETTER, 'itemSetter')?->getName());
        $this->assertSame('itemSetter', $object->findPropertyDto(ApproachEnum::SETTER, 'ITEMSETTER')?->getName());
        $this->assertInstanceOf(PropertyDto::class, $object->findPropertyDto(ApproachEnum::SETTER, 'itemAttributeTarget'));
        $this->assertSame('itemAttributeTarget', $object->findPropertyDto(ApproachEnum::SETTER, 'itemAttributeTarget')?->getName());
        $this->assertSame('itemAttributeTarget', $object->findPropertyDto(ApproachEnum::SETTER, 'itemAttributeTarget')?->getName());
    }
}
