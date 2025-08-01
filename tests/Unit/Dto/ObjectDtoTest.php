<?php

declare(strict_types=1);

namespace Unit\Dto;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Dto\AnnotationDto;
use Wundii\DataMapper\Dto\AttributeDto;
use Wundii\DataMapper\Dto\MethodDto;
use Wundii\DataMapper\Dto\ParameterDto;
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
        return new ReflectionObjectDto('', '', [], [], [], [], [], []);
    }

    public function objectComplex(): ReflectionObjectDto
    {
        return new ReflectionObjectDto(
            '',
            '',
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
                new PropertyDto(
                    AccessibleEnum::PRIVATE,
                    'itemPropertyConst',
                    'MockClasses\ItemConstructor',
                    'target3',
                    false,
                    isDefaultValueAvailable: true,
                    defaultValue: 'default',
                    annotationDto: new AnnotationDto([new ParameterDto('target', ['string'], true, 'leer')], [
                        'target' => 'itemAttributeSource',
                    ]),
                    attributes: [
                        new AttributeDto(AttributeOriginEnum::TARGET_PROPERTY, 'MockClasses\ItemConstructor', 'Wundii\DataMapper\Attribute\SourceData', [
                            'target' => 'itemAttributeSource',
                        ]),
                    ]
                ),
            ],
            [
                new MethodDto(MethodTypeEnum::GETTER, AccessibleEnum::PUBLIC, 'nameGetter', DataTypeEnum::STRING, null, true),
                new MethodDto(MethodTypeEnum::GETTER, AccessibleEnum::PUBLIC, 'dataGetter', DataTypeEnum::ARRAY, null, false),
                new MethodDto(
                    MethodTypeEnum::GETTER,
                    AccessibleEnum::PUBLIC,
                    'itemGetter',
                    'MockClasses\ItemConstructor',
                    null,
                    false,
                    returnTypes: ['string'],
                    annotationDto: new AnnotationDto(),
                    parameters: [
                        new ParameterDto('item', ['MockClasses\ItemConstructor'], true, 'defaultValue'),
                    ]
                ),
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

    public function testPropertiesClass(): void
    {
        $object = $this->objectComplex();

        $expectedProperties = [
            new PropertyDto(AccessibleEnum::PUBLIC, 'namePropertyClass', DataTypeEnum::STRING, 'target1', true),
            new PropertyDto(AccessibleEnum::PROTECTED, 'dataPropertyClass', DataTypeEnum::ARRAY, 'target2', false),
            new PropertyDto(AccessibleEnum::PRIVATE, 'itemPropertyClass', 'MockClasses\ItemConstructor', 'target3', false),
        ];
        $this->assertEquals($expectedProperties, $object->getPropertiesClass());
    }

    public function testPropertiesConst(): void
    {
        $object = $this->objectComplex();

        $expectedProperties = [
            new PropertyDto(AccessibleEnum::PUBLIC, 'namePropertyConst', DataTypeEnum::STRING, 'target1', true),
            new PropertyDto(AccessibleEnum::PROTECTED, 'dataPropertyConst', DataTypeEnum::ARRAY, 'target2', false),
            new PropertyDto(
                AccessibleEnum::PRIVATE,
                'itemPropertyConst',
                'MockClasses\ItemConstructor',
                'target3',
                false,
                isDefaultValueAvailable: true,
                defaultValue: 'default',
                annotationDto: new AnnotationDto([new ParameterDto('target', ['string'], true, 'leer')], [
                    'target' => 'itemAttributeSource',
                ]),
                attributes: [
                    new AttributeDto(AttributeOriginEnum::TARGET_PROPERTY, 'MockClasses\ItemConstructor', 'Wundii\DataMapper\Attribute\SourceData', [
                        'target' => 'itemAttributeSource',
                    ]),
                ]
            ),
        ];
        $this->assertEquals($expectedProperties, $object->getPropertiesConst());
    }

    public function testProperties(): void
    {
        $object = $this->objectComplex();

        $expectedProperties = [
            new PropertyDto(AccessibleEnum::PUBLIC, 'namePropertyClass', DataTypeEnum::STRING, 'target1', true),
            new PropertyDto(AccessibleEnum::PROTECTED, 'dataPropertyClass', DataTypeEnum::ARRAY, 'target2', false),
            new PropertyDto(AccessibleEnum::PRIVATE, 'itemPropertyClass', 'MockClasses\ItemConstructor', 'target3', false),
            new PropertyDto(AccessibleEnum::PUBLIC, 'namePropertyConst', DataTypeEnum::STRING, 'target1', true),
            new PropertyDto(AccessibleEnum::PROTECTED, 'dataPropertyConst', DataTypeEnum::ARRAY, 'target2', false),
            new PropertyDto(
                AccessibleEnum::PRIVATE,
                'itemPropertyConst',
                'MockClasses\ItemConstructor',
                'target3',
                false,
                isDefaultValueAvailable: true,
                defaultValue: 'default',
                annotationDto: new AnnotationDto([new ParameterDto('target', ['string'], true, 'leer')], [
                    'target' => 'itemAttributeSource',
                ]),
                attributes: [
                    new AttributeDto(AttributeOriginEnum::TARGET_PROPERTY, 'MockClasses\ItemConstructor', 'Wundii\DataMapper\Attribute\SourceData', [
                        'target' => 'itemAttributeSource',
                    ]),
                ]
            ),
        ];
        $this->assertEquals($expectedProperties, $object->getProperties());

        $first = array_key_first($object->getProperties());
        $last = array_key_last($object->getProperties());

        $firstProperty = $object->getProperties()[$first];
        $lastProperty = $object->getProperties()[$last];

        $this->assertFalse($firstProperty->isDefaultValueAvailable());
        $this->assertNull($firstProperty->getDefaultValue());
        $this->assertNull($firstProperty->getAnnotationDto());
        $this->assertTrue($lastProperty->isDefaultValueAvailable());
        $this->assertSame('default', $lastProperty->getDefaultValue());
        $this->assertInstanceOf(AnnotationDto::class, $lastProperty->getAnnotationDto());
        $this->assertSame(true, $lastProperty->getAnnotationDto()->getParameterDto()[0]->isDefaultValueAvailable());
        $this->assertSame('leer', $lastProperty->getAnnotationDto()->getParameterDto()[0]->getDefaultValue());
    }

    public function testAttributesClass(): void
    {
        $object = $this->objectComplex();

        $expectedAttributes = [
            new AttributeDto(AttributeOriginEnum::TARGET_CLASS, 'MockClasses\ItemConstructor1', 'MockClasses\ObjectAttribute', []),
            new AttributeDto(AttributeOriginEnum::TARGET_CLASS, 'MockClasses\ItemConstructor2', 'MockClasses\ObjectAttribute', []),
            new AttributeDto(AttributeOriginEnum::TARGET_CLASS, 'MockClasses\ItemConstructor3', 'MockClasses\ObjectAttribute', []),
        ];
        $this->assertEquals($expectedAttributes, $object->getAttributesClass());
    }

    public function testAttributes(): void
    {
        $object = $this->objectComplex();

        $expectedAttributes = [
            new AttributeDto(AttributeOriginEnum::TARGET_CLASS, 'MockClasses\ItemConstructor1', 'MockClasses\ObjectAttribute', []),
            new AttributeDto(AttributeOriginEnum::TARGET_CLASS, 'MockClasses\ItemConstructor2', 'MockClasses\ObjectAttribute', []),
            new AttributeDto(AttributeOriginEnum::TARGET_CLASS, 'MockClasses\ItemConstructor3', 'MockClasses\ObjectAttribute', []),
            new AttributeDto(AttributeOriginEnum::TARGET_PROPERTY, 'MockClasses\ItemConstructor', 'Wundii\DataMapper\Attribute\SourceData', [
                'target' => 'itemAttributeSource',
            ]),
            new AttributeDto(AttributeOriginEnum::TARGET_METHOD, 'MockClasses\ItemConstructor', 'Wundii\DataMapper\Attribute\TargetData', [
                'alias' => 'itemAttributeTarget',
            ]),
        ];
        $this->assertEquals($expectedAttributes, $object->getAttributes());

        $first = array_key_first($object->getAttributes());
        $last = array_key_last($object->getAttributes());

        $firstAttribute = $object->getAttributes()[$first];
        $lastAttribute = $object->getAttributes()[$last];

        $this->assertInstanceOf(AttributeDto::class, $firstAttribute);
        $this->assertSame(AttributeOriginEnum::TARGET_CLASS, $firstAttribute->getAttributeOriginEnum());
        $this->assertSame('MockClasses\ItemConstructor1', $firstAttribute->getOriginName());
        $this->assertInstanceOf(AttributeDto::class, $lastAttribute);
        $this->assertSame(AttributeOriginEnum::TARGET_METHOD, $lastAttribute->getAttributeOriginEnum());
        $this->assertSame('MockClasses\ItemConstructor', $lastAttribute->getOriginName());
    }

    public function testMethodOther(): void
    {
        $object = $this->objectComplex();

        $expectedMethods = [
            new MethodDto(MethodTypeEnum::OTHER, AccessibleEnum::PUBLIC, 'nameConstructor', DataTypeEnum::STRING, null, true),
            new MethodDto(MethodTypeEnum::OTHER, AccessibleEnum::PUBLIC, 'dataConstructor', DataTypeEnum::ARRAY, null, false),
            new MethodDto(MethodTypeEnum::OTHER, AccessibleEnum::PUBLIC, 'itemConstructor', 'MockClasses\ItemConstructor', null, false),
        ];
        $this->assertEquals($expectedMethods, $object->getMethodOther());
    }

    public function testMethodGetters(): void
    {
        $object = $this->objectComplex();

        $expectedGetters = [
            new MethodDto(MethodTypeEnum::GETTER, AccessibleEnum::PUBLIC, 'nameGetter', DataTypeEnum::STRING, null, true),
            new MethodDto(MethodTypeEnum::GETTER, AccessibleEnum::PUBLIC, 'dataGetter', DataTypeEnum::ARRAY, null, false),
            new MethodDto(
                MethodTypeEnum::GETTER,
                AccessibleEnum::PUBLIC,
                'itemGetter',
                'MockClasses\ItemConstructor',
                null,
                false,
                returnTypes: ['string'],
                annotationDto: new AnnotationDto(),
                parameters: [
                    new ParameterDto('item', ['MockClasses\ItemConstructor'], true, 'defaultValue'),
                ]
            ),
        ];
        $this->assertEquals($expectedGetters, $object->getMethodGetters());

        $first = array_key_first($object->getMethodGetters());
        $last = array_key_last($object->getMethodGetters());

        $firstMethod = $object->getMethodGetters()[$first];
        $lastMethod = $object->getMethodGetters()[$last];

        $this->assertInstanceOf(MethodDto::class, $firstMethod);
        $this->assertSame('nameGetter', $firstMethod->getterName());
        $this->assertCount(0, $firstMethod->getReturnTypes());
        $this->assertNull($firstMethod->getAnnotationDto());
        $this->assertCount(0, $firstMethod->getParameters());

        $this->assertInstanceOf(MethodDto::class, $lastMethod);
        $this->assertSame('itemGetter', $lastMethod->getterName());
        $this->assertCount(1, $lastMethod->getReturnTypes());
        $this->assertInstanceOf(AnnotationDto::class, $lastMethod->getAnnotationDto());
        $this->assertCount(1, $lastMethod->getParameters());
    }

    public function testMethodSetters(): void
    {
        $object = $this->objectComplex();

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
    }

    public function testMethods(): void
    {
        $object = $this->objectComplex();

        $expectedMethods = [
            new MethodDto(MethodTypeEnum::GETTER, AccessibleEnum::PUBLIC, 'nameGetter', DataTypeEnum::STRING, null, true),
            new MethodDto(MethodTypeEnum::GETTER, AccessibleEnum::PUBLIC, 'dataGetter', DataTypeEnum::ARRAY, null, false),
            new MethodDto(
                MethodTypeEnum::GETTER,
                AccessibleEnum::PUBLIC,
                'itemGetter',
                'MockClasses\ItemConstructor',
                null,
                false,
                returnTypes: ['string'],
                annotationDto: new AnnotationDto(),
                parameters: [
                    new ParameterDto('item', ['MockClasses\ItemConstructor'], true, 'defaultValue'),
                ]
            ),
            new MethodDto(MethodTypeEnum::SETTER, AccessibleEnum::PUBLIC, 'nameSetter', DataTypeEnum::STRING, null, true),
            new MethodDto(MethodTypeEnum::SETTER, AccessibleEnum::PUBLIC, 'dataSetter', DataTypeEnum::ARRAY, null, false),
            new MethodDto(MethodTypeEnum::SETTER, AccessibleEnum::PUBLIC, 'itemSetter', 'MockClasses\ItemConstructor', null, false, attributes: [
                new AttributeDto(AttributeOriginEnum::TARGET_METHOD, 'MockClasses\ItemConstructor', 'Wundii\DataMapper\Attribute\TargetData', [
                    'alias' => 'itemAttributeTarget',
                ]),
            ]),
            new MethodDto(MethodTypeEnum::OTHER, AccessibleEnum::PUBLIC, 'nameConstructor', DataTypeEnum::STRING, null, true),
            new MethodDto(MethodTypeEnum::OTHER, AccessibleEnum::PUBLIC, 'dataConstructor', DataTypeEnum::ARRAY, null, false),
            new MethodDto(MethodTypeEnum::OTHER, AccessibleEnum::PUBLIC, 'itemConstructor', 'MockClasses\ItemConstructor', null, false),
        ];
        $this->assertEquals($expectedMethods, $object->getMethods());
    }

    public function testAvailableData(): void
    {
        $object = $this->objectComplex();

        $expectedAvailableData = [
            'namePropertyClass' => new PropertyDto(AccessibleEnum::PUBLIC, 'namePropertyClass', DataTypeEnum::STRING, 'target1', true),
            'nameGetter' => new MethodDto(MethodTypeEnum::GETTER, AccessibleEnum::PUBLIC, 'nameGetter', DataTypeEnum::STRING, null, true),
            'itemGetter' => new MethodDto(
                MethodTypeEnum::GETTER,
                AccessibleEnum::PUBLIC,
                'itemGetter',
                'MockClasses\ItemConstructor',
                null,
                false,
                returnTypes: ['string'],
                annotationDto: new AnnotationDto(),
                parameters: [
                    new ParameterDto('item', ['MockClasses\ItemConstructor'], true, 'defaultValue'),
                ]
            ),
            'dataGetter' => new MethodDto(MethodTypeEnum::GETTER, AccessibleEnum::PUBLIC, 'dataGetter', DataTypeEnum::ARRAY, null, false),
            'namePropertyConst' => new PropertyDto(AccessibleEnum::PUBLIC, 'namePropertyConst', DataTypeEnum::STRING, 'target1', true),
        ];
        $this->assertEquals($expectedAvailableData, $object->availableData());
    }

    public function testFindElementDto(): void
    {
        $object = $this->objectComplex();

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
