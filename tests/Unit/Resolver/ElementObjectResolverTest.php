<?php

declare(strict_types=1);

namespace Unit\Resolver;

use Exception;
use MockClasses\ElementData;
use MockClasses\ItemConstructor;
use MockClasses\RootProperties;
use MockClasses\RootSetters;
use MockClasses\TestEnum;
use MockClasses\TestStringEnum;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\Elements\DataArray;
use Wundii\DataMapper\Elements\DataBool;
use Wundii\DataMapper\Elements\DataFloat;
use Wundii\DataMapper\Elements\DataInt;
use Wundii\DataMapper\Elements\DataObject;
use Wundii\DataMapper\Elements\DataString;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Resolver\ElementObjectResolver;

class ElementObjectResolverTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreateInstanceConstructor(): void
    {
        $dataConfig = new DataConfig(
            approachEnum: ApproachEnum::CONSTRUCTOR,
        );
        $elementData = new DataObject(
            'MockClasses\ItemConstructor',
            [],
            'destination',
        );

        $elementObjectResolver = new ElementObjectResolver();
        $return = $elementObjectResolver->createInstance($dataConfig, $elementData, [5.67, true]);

        $expected = new ItemConstructor(5.67, true);

        $this->assertInstanceOf(ItemConstructor::class, $return);
        $this->assertEquals($expected, $return);
    }

    /**
     * @throws Exception
     */
    public function testCreateInstanceProperty(): void
    {
        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        $elementData = new DataObject(
            'MockClasses\RootProperties',
            [],
            'destination',
        );

        $elementObjectResolver = new ElementObjectResolver();
        $return = $elementObjectResolver->createInstance($dataConfig, $elementData);
        $this->assertInstanceOf(RootProperties::class, $return);
    }

    /**
     * @throws Exception
     */
    public function testCreateInstanceSetter(): void
    {
        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $elementData = new DataObject(
            'MockClasses\RootSetters',
            [],
            'destination',
        );

        $elementObjectResolver = new ElementObjectResolver();
        $return = $elementObjectResolver->createInstance($dataConfig, $elementData);
        $this->assertInstanceOf(RootSetters::class, $return);
    }

    /**
     * @throws Exception
     */
    public function testCreateInstanceEnumException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Enum class must have a from method: ' . TestEnum::class);

        $dataConfig = new DataConfig(approachEnum: ApproachEnum::PROPERTY);
        $elementData = new DataObject(
            'MockClasses\TestEnum',
            [
                new DataString('two', 'destination'),
            ],
            'destination',
            true
        );

        $elementObjectResolver = new ElementObjectResolver();
        $elementObjectResolver->createInstance($dataConfig, $elementData, ['two']);
    }

    /**
     * @throws Exception
     */
    public function testCreateInstanceEnum(): void
    {
        $dataConfig = new DataConfig(approachEnum: ApproachEnum::PROPERTY);
        $elementData = new DataObject(
            'MockClasses\TestStringEnum',
            [
                new DataString('two', 'destination'),
            ],
            'destination',
            true
        );

        $expected = TestStringEnum::TWO;

        $elementObjectResolver = new ElementObjectResolver();
        $return = $elementObjectResolver->createInstance($dataConfig, $elementData, ['two']);
        $this->assertInstanceOf(TestStringEnum::class, $return);
        $this->assertEquals($expected, $return);
    }

    /**
     * @throws Exception
     */
    public function testMatchExceptionWithoutInterface()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ElementInterface not implemented: MockClasses\ElementData');

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $elementData = new ElementData();

        $elementArrayResolver = new ElementObjectResolver();
        $elementArrayResolver->matchValue($dataConfig, $elementData);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataBool()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $elementData = new DataBool(true, 'destination');

        $elementObjectResolver = new ElementObjectResolver();
        $result = $elementObjectResolver->matchValue($dataConfig, $elementData);

        $this->assertTrue($result);

        $elementData = new DataBool(false, 'destination');

        $elementObjectResolver = new ElementObjectResolver();
        $result = $elementObjectResolver->matchValue($dataConfig, $elementData);

        $this->assertFalse($result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataInt()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $elementData = new DataInt(11, 'destination');

        $elementObjectResolver = new ElementObjectResolver();
        $result = $elementObjectResolver->matchValue($dataConfig, $elementData);

        $this->assertSame(11, $result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataFloat()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $elementData = new DataFloat(22.2, 'destination');

        $elementObjectResolver = new ElementObjectResolver();
        $result = $elementObjectResolver->matchValue($dataConfig, $elementData);

        $this->assertSame(22.2, $result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataString()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $elementData = new DataString('Value', 'destination');

        $elementObjectResolver = new ElementObjectResolver();
        $result = $elementObjectResolver->matchValue($dataConfig, $elementData);

        $this->assertSame('Value', $result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataArray()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $elementData = new DataArray([
            new DataString('value1', 'destination1'),
            new DataBool(false, 'destination2'),
        ], 'destination');

        $elementObjectResolver = new ElementObjectResolver();
        $result = $elementObjectResolver->matchValue($dataConfig, $elementData);

        $this->assertSame(['value1', false], $result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataObjectConstructor()
    {
        $dataConfig = new DataConfig(
            approachEnum: ApproachEnum::CONSTRUCTOR,
        );
        $elementData = new DataObject(
            'MockClasses\ItemConstructor',
            [
                new DataFloat(4.4, 'price'),
                new DataBool(false, 'isAvailable'),
            ],
            'destination',
        );

        $elementObjectResolver = new ElementObjectResolver();
        $result = $elementObjectResolver->matchValue($dataConfig, $elementData);

        $this->assertEquals(new ItemConstructor(4.4, false), $result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataObjectProperty()
    {
        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        $elementData = new DataObject(
            'MockClasses\RootProperties',
            [
                new DataInt(4, 'id'),
                new DataString('test', 'name'),
            ],
            'destination',
        );

        $elementObjectResolver = new ElementObjectResolver();
        $result = $elementObjectResolver->matchValue($dataConfig, $elementData);

        $expected = new RootProperties();
        $expected->name = 'test';
        $expected->id = 4;

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataObjectSetter()
    {
        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $elementData = new DataObject(
            'MockClasses\RootSetters',
            [
                new DataInt(4, 'setId'),
                new DataString('test', 'setName'),
            ],
            'destination',
        );

        $elementObjectResolver = new ElementObjectResolver();
        $result = $elementObjectResolver->matchValue($dataConfig, $elementData);

        $expected = new RootSetters();
        $expected->setId(4);
        $expected->setName('test');

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws Exception
     */
    public function testResolveConstructorException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('You can not use constructor approach with an object');

        $dataConfig = new DataConfig(
            approachEnum: ApproachEnum::CONSTRUCTOR,
        );
        $elementData = new DataObject(
            new ItemConstructor(4.4, false),
            [
                new DataFloat(4.4, 'price'),
                new DataBool(false, 'isAvailable'),
            ],
            'destination',
        );

        $elementObjectResolver = new ElementObjectResolver();
        $elementObjectResolver->resolve($dataConfig, $elementData);
    }

    /**
     * @throws Exception
     */
    public function testResolveConstructor()
    {
        $dataConfig = new DataConfig(
            approachEnum: ApproachEnum::CONSTRUCTOR,
        );
        $elementData = new DataObject(
            'MockClasses\ItemConstructor',
            [
                new DataFloat(4.4, 'price'),
                new DataBool(false, 'isAvailable'),
            ],
            'destination',
        );

        $elementObjectResolver = new ElementObjectResolver();
        $result = $elementObjectResolver->resolve($dataConfig, $elementData);

        $this->assertEquals(new ItemConstructor(4.4, false), $result);
    }

    /**
     * @throws Exception
     */
    public function testResolvePropertyException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Destination is not declared');

        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        $elementData = new DataObject(
            'MockClasses\RootProperties',
            [
                new DataInt(4),
                new DataString('test'),
            ],
            'destination',
        );

        $elementObjectResolver = new ElementObjectResolver();
        $elementObjectResolver->resolve($dataConfig, $elementData);
    }

    /**
     * @throws Exception
     */
    public function testResolvePropertyWithClassString()
    {
        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        $elementData = new DataObject(
            'MockClasses\RootProperties',
            [
                new DataInt(4, 'id'),
                new DataString('test', 'name'),
            ],
            'destination',
        );

        $elementObjectResolver = new ElementObjectResolver();
        $result = $elementObjectResolver->resolve($dataConfig, $elementData);

        $expected = new RootProperties();
        $expected->name = 'test';
        $expected->id = 4;

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws Exception
     */
    public function testResolvePropertyWithObject()
    {
        $object = new RootProperties();
        $object->id = 999;

        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        $elementData = new DataObject(
            $object,
            [
                new DataInt(4, 'id'),
                new DataString('test', 'name'),
            ],
            'destination',
        );

        $elementObjectResolver = new ElementObjectResolver();
        $result = $elementObjectResolver->resolve($dataConfig, $elementData);

        $expected = new RootProperties();
        $expected->name = 'test';
        $expected->id = 4;

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws Exception
     */
    public function testResolveSetterException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Destination is not declared');

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $elementData = new DataObject(
            'MockClasses\RootSetters',
            [
                new DataInt(4),
                new DataString('test'),
            ],
            'destination',
        );

        $elementObjectResolver = new ElementObjectResolver();
        $elementObjectResolver->resolve($dataConfig, $elementData);
    }

    /**
     * @throws Exception
     */
    public function testResolveSetterWithClassString()
    {
        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $elementData = new DataObject(
            'MockClasses\RootSetters',
            [
                new DataInt(4, 'setId'),
                new DataString('test', 'setName'),
            ],
            'destination',
        );

        $elementObjectResolver = new ElementObjectResolver();
        $result = $elementObjectResolver->resolve($dataConfig, $elementData);

        $expected = new RootSetters();
        $expected->setId(4);
        $expected->setName('test');

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws Exception
     */
    public function testResolveSetterWithObject()
    {
        $object = new RootSetters();
        $object->setId(999);

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $elementData = new DataObject(
            $object,
            [
                new DataInt(4, 'setId'),
                new DataString('test', 'setName'),
            ],
            'destination',
        );

        $elementObjectResolver = new ElementObjectResolver();
        $result = $elementObjectResolver->resolve($dataConfig, $elementData);

        $expected = new RootSetters();
        $expected->setId(4);
        $expected->setName('test');

        $this->assertEquals($expected, $result);
    }
}
