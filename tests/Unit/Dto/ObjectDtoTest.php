<?php

declare(strict_types=1);

namespace Unit\Dto;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Attribute\SourceData;
use Wundii\DataMapper\Attribute\TargetData;
use Wundii\DataMapper\Dto\ObjectPropertyDto;
use Wundii\DataMapper\Dto\PropertyDto;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ApproachEnum;
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
                new PropertyDto('nameProperty', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
                new PropertyDto('dataProperty', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
                new PropertyDto('itemProperty', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
            ],
            [
                new PropertyDto('nameConstructor', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
                new PropertyDto('dataConstructor', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
                new PropertyDto('itemConstructor', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
            ],
            [
                new PropertyDto('nameGetter', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
                new PropertyDto('dataGetter', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
                new PropertyDto('itemGetter', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
            ],
            [
                new PropertyDto('nameSetter', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
                new PropertyDto('dataSetter', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
                new PropertyDto('itemSetter', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
            ],
            [
                new PropertyDto('nameAttributeSource', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC, attributeClassString: SourceData::class),
                new PropertyDto('dataAttributeSource', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED, attributeClassString: SourceData::class),
                new PropertyDto('itemAttributeSource', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE, attributeClassString: SourceData::class),
                new PropertyDto('nameAttributeTarget', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC, attributeClassString: TargetData::class),
                new PropertyDto('dataAttributeTarget', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED, attributeClassString: TargetData::class),
                new PropertyDto('itemAttributeTarget', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE, attributeClassString: TargetData::class),
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
            new PropertyDto('nameProperty', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
            new PropertyDto('dataProperty', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
            new PropertyDto('itemProperty', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
        ];
        $this->assertEquals($expectedProperties, $object->getProperties());

        $expectedConstructors = [
            new PropertyDto('nameConstructor', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
            new PropertyDto('dataConstructor', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
            new PropertyDto('itemConstructor', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
        ];
        $this->assertEquals($expectedConstructors, $object->getConstructor());

        $expectedGetters = [
            new PropertyDto('nameGetter', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
            new PropertyDto('dataGetter', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
            new PropertyDto('itemGetter', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
        ];
        $this->assertEquals($expectedGetters, $object->getGetters());

        $expectedSetters = [
            new PropertyDto('nameSetter', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
            new PropertyDto('dataSetter', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED),
            new PropertyDto('itemSetter', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE),
        ];
        $this->assertEquals($expectedSetters, $object->getSetters());

        $expectedAttribute = [
            new PropertyDto('nameAttributeSource', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC, attributeClassString: SourceData::class),
            new PropertyDto('dataAttributeSource', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED, attributeClassString: SourceData::class),
            new PropertyDto('itemAttributeSource', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE, attributeClassString: SourceData::class),
            new PropertyDto('nameAttributeTarget', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC, attributeClassString: TargetData::class),
            new PropertyDto('dataAttributeTarget', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED, attributeClassString: TargetData::class),
            new PropertyDto('itemAttributeTarget', 'MockClasses\ItemConstructor', 'target3', true, false, AccessibleEnum::PRIVATE, attributeClassString: TargetData::class),
        ];
        $this->assertEquals($expectedAttribute, $object->getAttributes());

        $expectedAvailableData = [
            'nameProperty' => new PropertyDto('nameProperty', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
            'nameGetter' => new PropertyDto('nameGetter', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC),
            'nameAttributeSource' => new PropertyDto('nameAttributeSource', DataTypeEnum::STRING, 'target1', false, true, AccessibleEnum::PUBLIC, attributeClassString: SourceData::class),
        ];
        $this->assertEquals($expectedAvailableData, $object->availableData());

        $expectedAttributeTargetType = new PropertyDto('dataAttributeTarget', DataTypeEnum::ARRAY, 'target2', true, false, AccessibleEnum::PROTECTED, attributeClassString: TargetData::class);
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
