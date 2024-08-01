<?php

declare(strict_types=1);

namespace Unit\Resolver;

use Exception;
use MockClasses\ElementData;
use MockClasses\ItemConstructor;
use MockClasses\RootProperties;
use MockClasses\RootSetters;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\Elements\DataArray;
use Wundii\DataMapper\Elements\DataBool;
use Wundii\DataMapper\Elements\DataFloat;
use Wundii\DataMapper\Elements\DataInt;
use Wundii\DataMapper\Elements\DataObject;
use Wundii\DataMapper\Elements\DataString;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Resolver\ElementArrayResolver;

class ElementArrayResolverTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testMatchExceptionWithoutInterface()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ElementInterface not implemented: MockClasses\ElementData');

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $elementData = new ElementData();

        $elementArrayResolver = new ElementArrayResolver();
        $elementArrayResolver->matchValue($dataConfig, $elementData);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataBool()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $elementData = new DataBool(true, 'destination');

        $elementArrayResolver = new ElementArrayResolver();
        $result = $elementArrayResolver->matchValue($dataConfig, $elementData);

        $this->assertTrue($result);

        $elementData = new DataBool(false, 'destination');

        $elementArrayResolver = new ElementArrayResolver();
        $result = $elementArrayResolver->matchValue($dataConfig, $elementData);

        $this->assertFalse($result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataInt()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $elementData = new DataInt(11, 'destination');

        $elementArrayResolver = new ElementArrayResolver();
        $result = $elementArrayResolver->matchValue($dataConfig, $elementData);

        $this->assertSame(11, $result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataFloat()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $elementData = new DataFloat(22.2, 'destination');

        $elementArrayResolver = new ElementArrayResolver();
        $result = $elementArrayResolver->matchValue($dataConfig, $elementData);

        $this->assertSame(22.2, $result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataString()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $elementData = new DataString('Value', 'destination');

        $elementArrayResolver = new ElementArrayResolver();
        $result = $elementArrayResolver->matchValue($dataConfig, $elementData);

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

        $elementArrayResolver = new ElementArrayResolver();
        $result = $elementArrayResolver->matchValue($dataConfig, $elementData);

        $this->assertSame(['value1', false], $result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataObjectConstructor()
    {
        $dataConfig = new DataConfig(
            approachEnum: ApproachEnum::CONSTRUCTOR
        );
        $elementData = new DataObject(
            'MockClasses\ItemConstructor',
            [
                new DataFloat(4.4, 'price'),
                new DataBool(false, 'isAvailable'),
            ],
            'destination',
        );

        $elementArrayResolver = new ElementArrayResolver();
        $result = $elementArrayResolver->matchValue($dataConfig, $elementData);

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

        $elementArrayResolver = new ElementArrayResolver();
        $result = $elementArrayResolver->matchValue($dataConfig, $elementData);

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

        $elementArrayResolver = new ElementArrayResolver();
        $result = $elementArrayResolver->matchValue($dataConfig, $elementData);

        $expected = new RootSetters();
        $expected->setId(4);
        $expected->setName('test');

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws Exception
     */
    public function testResolveDataArrayEmpty()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $elementData = new DataArray([]);

        $elementArrayResolver = new ElementArrayResolver();
        $result = $elementArrayResolver->resolve($dataConfig, $elementData);

        $this->assertEquals([], $result);
    }

    /**
     * @throws Exception
     */
    public function testResolveDataArray()
    {
        $dataConfig = new DataConfig(
            approachEnum: ApproachEnum::CONSTRUCTOR
        );
        $elementData = new DataArray([
            new DataString('value1', 'destination1'),
            new DataBool(false, 'destination2'),
            new DataInt(1, 'destination3'),
            new DataFloat(2.2, 'destination4'),
            new DataArray([
                new DataString('value2', 'destination5'),
                new DataBool(true, 'destination6'),
            ], 'destination7'),
            new DataObject(
                'MockClasses\ItemConstructor',
                [
                    new DataFloat(2.2, 'price'),
                    new DataBool(true, 'isAvailable'),
                ],
                'destination5',
            ),
        ]);

        $elementArrayResolver = new ElementArrayResolver();
        $result = $elementArrayResolver->resolve($dataConfig, $elementData);

        $expected = [
            'value1',
            false,
            1,
            2.2,
            [
                'value2',
                true,
            ],
            new ItemConstructor(2.2, true),
        ];

        $this->assertEquals($expected, $result);
    }
}
