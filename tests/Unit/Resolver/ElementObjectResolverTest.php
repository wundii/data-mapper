<?php

declare(strict_types=1);

namespace DataMapper\Tests\Unit\Resolver;

use DataMapper\DataConfig;
use DataMapper\Elements\DataArray;
use DataMapper\Elements\DataBool;
use DataMapper\Elements\DataFloat;
use DataMapper\Elements\DataInt;
use DataMapper\Elements\DataObject;
use DataMapper\Elements\DataString;
use DataMapper\Enum\ApproachEnum;
use DataMapper\Resolver\ElementObjectResolver;
use DataMapper\Tests\MockClasses\ItemConstructor;
use DataMapper\Tests\MockClasses\RootProperties;
use DataMapper\Tests\MockClasses\RootSetters;
use DateTime;
use DateTimeInterface;
use Exception;
use PHPUnit\Framework\TestCase;

class ElementObjectResolverTest extends TestCase
{
    public function testCreateInstanceConstructor(): void
    {
        $dataConfig = new DataConfig();
        $elementData = new DataObject(
            'DataMapper\Tests\MockClasses\ItemConstructor',
            [],
            'destination',
        );

        $elementObjectResolver = new ElementObjectResolver();
        $return = $elementObjectResolver->createInstance($dataConfig, $elementData, [5.67, true]);

        $expected = new ItemConstructor(5.67, true);

        $this->assertInstanceOf(ItemConstructor::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testCreateInstanceProperty(): void
    {
        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        $elementData = new DataObject(
            'DataMapper\Tests\MockClasses\RootProperties',
            [],
            'destination',
        );

        $elementObjectResolver = new ElementObjectResolver();
        $return = $elementObjectResolver->createInstance($dataConfig, $elementData);
        $this->assertInstanceOf(RootProperties::class, $return);
    }

    public function testCreateInstanceSetter(): void
    {
        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $elementData = new DataObject(
            'DataMapper\Tests\MockClasses\RootSetters',
            [],
            'destination',
        );

        $elementObjectResolver = new ElementObjectResolver();
        $return = $elementObjectResolver->createInstance($dataConfig, $elementData);
        $this->assertInstanceOf(RootSetters::class, $return);
    }

    public function testCreateInstanceClassMap(): void
    {
        $dataConfig = new DataConfig(
            approachEnum: ApproachEnum::PROPERTY,
            classMap: [
                DateTimeInterface::class => DateTime::class,
            ],
        );
        $elementData = new DataObject(
            'DateTimeInterface',
            [],
            'destination',
        );

        $elementObjectResolver = new ElementObjectResolver();
        $return = $elementObjectResolver->createInstance($dataConfig, $elementData);
        $this->assertInstanceOf(DateTime::class, $return);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataBool()
    {
        $dataConfig = new DataConfig();
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
        $dataConfig = new DataConfig();
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
        $dataConfig = new DataConfig();
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
        $dataConfig = new DataConfig();
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
        $dataConfig = new DataConfig();
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
        $dataConfig = new DataConfig();
        $elementData = new DataObject(
            'DataMapper\Tests\MockClasses\ItemConstructor',
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
            'DataMapper\Tests\MockClasses\RootProperties',
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
            'DataMapper\Tests\MockClasses\RootSetters',
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

        $dataConfig = new DataConfig();
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
        $dataConfig = new DataConfig();
        $elementData = new DataObject(
            'DataMapper\Tests\MockClasses\ItemConstructor',
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
            'DataMapper\Tests\MockClasses\RootProperties',
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
    public function testResolveProperty()
    {
        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        $elementData = new DataObject(
            'DataMapper\Tests\MockClasses\RootProperties',
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
            'DataMapper\Tests\MockClasses\RootSetters',
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
    public function testResolveSetter()
    {
        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $elementData = new DataObject(
            'DataMapper\Tests\MockClasses\RootSetters',
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
