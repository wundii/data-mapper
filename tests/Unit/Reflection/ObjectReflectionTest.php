<?php

declare(strict_types=1);

namespace Unit\Reflection;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Dto\ObjectDto;
use Wundii\DataMapper\Dto\PropertyDto;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Enum\DataTypeEnum;
use Wundii\DataMapper\Enum\VisibilityEnum;

class ObjectReflectionTest extends TestCase
{
    public function objectEmpty(): ObjectDto
    {
        return new ObjectDto([], [], [], [], []);
    }

    public function objectComplex(): ObjectDto
    {
        return new ObjectDto(
            [
                new PropertyDto('nameProperty', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
                new PropertyDto('dataProperty', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
                new PropertyDto('itemProperty', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
            ],
            [
                new PropertyDto('nameConstructor', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
                new PropertyDto('dataConstructor', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
                new PropertyDto('itemConstructor', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
            ],
            [
                new PropertyDto('nameGetter', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
                new PropertyDto('dataGetter', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
                new PropertyDto('itemGetter', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
            ],
            [
                new PropertyDto('nameSetter', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
                new PropertyDto('dataSetter', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
                new PropertyDto('itemSetter', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
            ],
            [
                new PropertyDto('nameAttribute', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
                new PropertyDto('dataAttribute', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
                new PropertyDto('itemAttribute', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
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
            new PropertyDto('nameProperty', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
            new PropertyDto('dataProperty', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
            new PropertyDto('itemProperty', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
        ];
        $this->assertEquals($expectedProperties, $object->getProperties());

        $expectedConstructors = [
            new PropertyDto('nameConstructor', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
            new PropertyDto('dataConstructor', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
            new PropertyDto('itemConstructor', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
        ];
        $this->assertEquals($expectedConstructors, $object->getConstructor());

        $expectedGetters = [
            new PropertyDto('nameGetter', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
            new PropertyDto('dataGetter', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
            new PropertyDto('itemGetter', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
        ];
        $this->assertEquals($expectedGetters, $object->getGetters());

        $expectedSetters = [
            new PropertyDto('nameSetter', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
            new PropertyDto('dataSetter', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
            new PropertyDto('itemSetter', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
        ];
        $this->assertEquals($expectedSetters, $object->getSetters());

        $expectedAttribute = [
            new PropertyDto('nameAttribute', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
            new PropertyDto('dataAttribute', DataTypeEnum::ARRAY, 'target2', true, false, VisibilityEnum::PROTECTED),
            new PropertyDto('itemAttribute', 'MockClasses\ItemConstructor', 'target3', true, false, VisibilityEnum::PRIVATE),
        ];
        $this->assertEquals($expectedAttribute, $object->getAttributes());

        $expectedAvailableData = [
            'nameProperty' => new PropertyDto('nameProperty', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
            'nameGetter' => new PropertyDto('nameGetter', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
            'nameAttribute' => new PropertyDto('nameAttribute', DataTypeEnum::STRING, 'target1', false, true, VisibilityEnum::PUBLIC),
        ];
        $this->assertEquals($expectedAvailableData, $object->availableData());

        $this->assertInstanceOf(PropertyDto::class, $object->findPropertyDto(ApproachEnum::PROPERTY, 'nameProperty'));
        $this->assertSame('nameProperty', $object->findPropertyDto(ApproachEnum::PROPERTY, 'nameProperty')?->getName());
        $this->assertSame('nameProperty', $object->findPropertyDto(ApproachEnum::PROPERTY, 'NAMEPROPERTY')?->getName());
        $this->assertInstanceOf(PropertyDto::class, $object->findPropertyDto(ApproachEnum::CONSTRUCTOR, 'dataConstructor'));
        $this->assertSame('dataConstructor', $object->findPropertyDto(ApproachEnum::CONSTRUCTOR, 'dataConstructor')?->getName());
        $this->assertSame('dataConstructor', $object->findPropertyDto(ApproachEnum::CONSTRUCTOR, 'DATACONSTRUCTOR')?->getName());
        $this->assertInstanceOf(PropertyDto::class, $object->findPropertyDto(ApproachEnum::SETTER, 'itemSetter'));
        $this->assertSame('itemSetter', $object->findPropertyDto(ApproachEnum::SETTER, 'itemSetter')?->getName());
        $this->assertSame('itemSetter', $object->findPropertyDto(ApproachEnum::SETTER, 'ITEMSETTER')?->getName());
    }
}
