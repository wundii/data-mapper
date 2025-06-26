<?php

declare(strict_types=1);

namespace Unit\Dto;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Dto\AttributeDto;
use Wundii\DataMapper\Dto\MethodDto;
use Wundii\DataMapper\Dto\PropertyDto;
use Wundii\DataMapper\Dto\ReflectionObjectDto;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Enum\AttributeOriginEnum;
use Wundii\DataMapper\Enum\DataTypeEnum;
use Wundii\DataMapper\Enum\MethodTypeEnum;

class ObjectDtoTest extends TestCase
{
    public function objectEmpty(): ReflectionObjectDto
    {
        return new ReflectionObjectDto([], [], [], [], [], []);
    }

    public function objectComplex(): ReflectionObjectDto
    {
        return new ReflectionObjectDto(
            [
                new AttributeDto(AttributeOriginEnum::TARGET_CLASS, 'MockClasses\ItemConstructor1', 'MockClasses\ObjectAttribute', []),
                new AttributeDto(AttributeOriginEnum::TARGET_CLASS, 'MockClasses\ItemConstructor2', 'MockClasses\ObjectAttribute', []),
                new AttributeDto(AttributeOriginEnum::TARGET_CLASS, 'MockClasses\ItemConstructor3', 'MockClasses\ObjectAttribute', []),
            ],
            [
                new PropertyDto(AccessibleEnum::PUBLIC, 'namePropertyClass', DataTypeEnum::STRING, 'target1', true),
                new PropertyDto(AccessibleEnum::PROTECTED, 'dataPropertyClass', DataTypeEnum::ARRAY, 'target2', false),
                new PropertyDto(AccessibleEnum::PRIVATE, 'itemPropertyClass', 'MockClasses\ItemConstructor', 'target3', false),
            ],
            [
                new PropertyDto(AccessibleEnum::PUBLIC, 'namePropertyConst', DataTypeEnum::STRING, 'target1', true),
                new PropertyDto(AccessibleEnum::PROTECTED, 'dataPropertyConst', DataTypeEnum::ARRAY, 'target2', false),
                new PropertyDto(AccessibleEnum::PRIVATE, 'itemPropertyConst', 'MockClasses\ItemConstructor', 'target3', false),
            ],
            [
                new MethodDto(MethodTypeEnum::GETTER, AccessibleEnum::PUBLIC, 'nameGetter', DataTypeEnum::STRING, null, true),
                new MethodDto(MethodTypeEnum::GETTER, AccessibleEnum::PUBLIC, 'dataGetter', DataTypeEnum::ARRAY, null, false),
                new MethodDto(MethodTypeEnum::GETTER, AccessibleEnum::PUBLIC, 'itemGetter', 'MockClasses\ItemConstructor', null, false),
            ],
            [
                new MethodDto(MethodTypeEnum::OTHER, AccessibleEnum::PUBLIC, 'nameConstructor', DataTypeEnum::STRING, null, true),
                new MethodDto(MethodTypeEnum::OTHER, AccessibleEnum::PUBLIC, 'dataConstructor', DataTypeEnum::ARRAY, null, false),
                new MethodDto(MethodTypeEnum::OTHER, AccessibleEnum::PUBLIC, 'itemConstructor', 'MockClasses\ItemConstructor', null, false),
            ],
            [
                new MethodDto(MethodTypeEnum::SETTER, AccessibleEnum::PUBLIC, 'nameSetter', DataTypeEnum::STRING, null, true),
                new MethodDto(MethodTypeEnum::SETTER, AccessibleEnum::PUBLIC, 'dataSetter', DataTypeEnum::ARRAY, null, false),
                new MethodDto(MethodTypeEnum::SETTER, AccessibleEnum::PUBLIC, 'itemSetter', 'MockClasses\ItemConstructor', null, false, attributes: [
                    new AttributeDto(AttributeOriginEnum::TARGET_METHOD, 'MockClasses\ItemConstructor', 'Wundii\DataMapper\Attribute\TargetData', [
                        'alias' => 'itemAttributeTarget',
                    ]),
                ]),
            ],
        );
    }

    public function testFindEmpty(): void
    {
        $object = $this->objectEmpty();

        $this->assertEquals([], $object->getPropertiesClass());
        $this->assertEquals([], $object->getPropertiesConst());
        $this->assertEquals([], $object->getMethodSetters());

        $this->assertNull($object->findElementDto(ApproachEnum::PROPERTY, 'nameProperty'));
        $this->assertNull($object->findElementDto(ApproachEnum::PROPERTY, 'NAMEPROPERTY'));
        $this->assertNull($object->findElementDto(ApproachEnum::CONSTRUCTOR, 'dataConstructor'));
        $this->assertNull($object->findElementDto(ApproachEnum::CONSTRUCTOR, 'DATACONSTRUCTOR'));
        $this->assertNull($object->findElementDto(ApproachEnum::SETTER, 'itemSetter'));
        $this->assertNull($object->findElementDto(ApproachEnum::SETTER, 'ITEMSETTER'));
    }

    public function testFind(): void
    {
        $object = $this->objectComplex();

        $expectedProperties = [
            new PropertyDto(AccessibleEnum::PUBLIC, 'namePropertyClass', DataTypeEnum::STRING, 'target1', true),
            new PropertyDto(AccessibleEnum::PROTECTED, 'dataPropertyClass', DataTypeEnum::ARRAY, 'target2', false),
            new PropertyDto(AccessibleEnum::PRIVATE, 'itemPropertyClass', 'MockClasses\ItemConstructor', 'target3', false),
        ];
        $this->assertEquals($expectedProperties, $object->getPropertiesClass());

        $expectedProperties = [
            new PropertyDto(AccessibleEnum::PUBLIC, 'namePropertyConst', DataTypeEnum::STRING, 'target1', true),
            new PropertyDto(AccessibleEnum::PROTECTED, 'dataPropertyConst', DataTypeEnum::ARRAY, 'target2', false),
            new PropertyDto(AccessibleEnum::PRIVATE, 'itemPropertyConst', 'MockClasses\ItemConstructor', 'target3', false),
        ];
        $this->assertEquals($expectedProperties, $object->getPropertiesConst());

        $expectedProperties = [
            new AttributeDto(AttributeOriginEnum::TARGET_CLASS, 'MockClasses\ItemConstructor1', 'MockClasses\ObjectAttribute', []),
            new AttributeDto(AttributeOriginEnum::TARGET_CLASS, 'MockClasses\ItemConstructor2', 'MockClasses\ObjectAttribute', []),
            new AttributeDto(AttributeOriginEnum::TARGET_CLASS, 'MockClasses\ItemConstructor3', 'MockClasses\ObjectAttribute', []),
        ];
        $this->assertEquals($expectedProperties, $object->getAttributesClass());

        $expectedMethodOther = [
            new MethodDto(MethodTypeEnum::OTHER, AccessibleEnum::PUBLIC, 'nameConstructor', DataTypeEnum::STRING, null, true),
            new MethodDto(MethodTypeEnum::OTHER, AccessibleEnum::PUBLIC, 'dataConstructor', DataTypeEnum::ARRAY, null, false),
            new MethodDto(MethodTypeEnum::OTHER, AccessibleEnum::PUBLIC, 'itemConstructor', 'MockClasses\ItemConstructor', null, false),
        ];
        $this->assertEquals($expectedMethodOther, $object->getMethodOther());

        $expectedGetters = [
            new MethodDto(MethodTypeEnum::GETTER, AccessibleEnum::PUBLIC, 'nameGetter', DataTypeEnum::STRING, null, true),
            new MethodDto(MethodTypeEnum::GETTER, AccessibleEnum::PUBLIC, 'dataGetter', DataTypeEnum::ARRAY, null, false),
            new MethodDto(MethodTypeEnum::GETTER, AccessibleEnum::PUBLIC, 'itemGetter', 'MockClasses\ItemConstructor', null, false),
        ];
        $this->assertEquals($expectedGetters, $object->getMethodGetters());

        $expectedSetters = [
            new MethodDto(MethodTypeEnum::SETTER, AccessibleEnum::PUBLIC, 'nameSetter', DataTypeEnum::STRING, null, true),
            new MethodDto(MethodTypeEnum::SETTER, AccessibleEnum::PUBLIC, 'dataSetter', DataTypeEnum::ARRAY, null, false),
            new MethodDto(MethodTypeEnum::SETTER, AccessibleEnum::PUBLIC, 'itemSetter', 'MockClasses\ItemConstructor', null, false, attributes: [
                new AttributeDto(AttributeOriginEnum::TARGET_METHOD, 'MockClasses\ItemConstructor', 'Wundii\DataMapper\Attribute\TargetData', [
                    'alias' => 'itemAttributeTarget',
                ]),
            ]),
        ];
        $this->assertEquals($expectedSetters, $object->getMethodSetters());

        $expectedAvailableData = [
            'namePropertyClass' => new PropertyDto(AccessibleEnum::PUBLIC, 'namePropertyClass', DataTypeEnum::STRING, 'target1', true),
            'nameGetter' => new MethodDto(MethodTypeEnum::GETTER, AccessibleEnum::PUBLIC, 'nameGetter', DataTypeEnum::STRING, null, true),
            'itemGetter' => new MethodDto(MethodTypeEnum::GETTER, AccessibleEnum::PUBLIC, 'itemGetter', 'MockClasses\ItemConstructor', null, false),
            'dataGetter' => new MethodDto(MethodTypeEnum::GETTER, AccessibleEnum::PUBLIC, 'dataGetter', DataTypeEnum::ARRAY, null, false),
            'namePropertyConst' => new PropertyDto(AccessibleEnum::PUBLIC, 'namePropertyConst', DataTypeEnum::STRING, 'target1', true),
        ];
        $this->assertEquals($expectedAvailableData, $object->availableData());

        $this->assertInstanceOf(PropertyDto::class, $object->findElementDto(ApproachEnum::PROPERTY, 'namePropertyClass'));
        $this->assertSame('namePropertyClass', $object->findElementDto(ApproachEnum::PROPERTY, 'namePropertyClass')?->getName());
        $this->assertSame('namePropertyClass', $object->findElementDto(ApproachEnum::PROPERTY, 'NAMEPROPERTYCLASS')?->getName());
        $this->assertInstanceOf(PropertyDto::class, $object->findElementDto(ApproachEnum::CONSTRUCTOR, 'dataPropertyConst'));
        $this->assertSame('dataPropertyConst', $object->findElementDto(ApproachEnum::CONSTRUCTOR, 'dataPropertyConst')?->getName());
        $this->assertSame('dataPropertyConst', $object->findElementDto(ApproachEnum::CONSTRUCTOR, 'DATAPROPERTYCONST')?->getName());
        $this->assertInstanceOf(MethodDto::class, $object->findElementDto(ApproachEnum::SETTER, 'itemSetter'));
        $this->assertSame('itemSetter', $object->findElementDto(ApproachEnum::SETTER, 'itemSetter')?->getName());
        $this->assertSame('itemSetter', $object->findElementDto(ApproachEnum::SETTER, 'ITEMSETTER')?->getName());
        $this->assertInstanceOf(MethodDto::class, $object->findElementDto(ApproachEnum::SETTER, 'itemAttributeTarget'));
        $this->assertSame('itemSetter', $object->findElementDto(ApproachEnum::SETTER, 'itemAttributeTarget')?->getName());
        $this->assertSame('itemSetter', $object->findElementDto(ApproachEnum::SETTER, 'itemAttributeTarget')?->getName());
    }
}
