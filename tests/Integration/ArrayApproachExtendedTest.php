<?php

declare(strict_types=1);

namespace Integration;

use Integration\Objects\ApproachBasic\BaseConstructor;
use Integration\Objects\ApproachBasic\SharedSubSetter;
use Integration\Objects\ApproachBasic\SubConstructor;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;

class ArrayApproachExtendedTest extends TestCase
{
    public function testConstructorDefault(): void
    {
        $array = [
            [
                'myStrings' => [
                    'hello',
                    'world',
                ],
                'amount' => 222.22,
                'name' => 'approach',
                'subConstructor' => [
                    'active' => true,
                ],
                'subConstructors' => [
                    [
                        'active' => true,
                    ],
                    [
                        'active' => false,
                    ],
                ],
                'id' => 1337,
            ],
            [
                'name' => 'approach',
                'myStrings' => [
                    'hello',
                    'next',
                    'world',
                ],
                'subConstructors' => [
                    [
                        'active' => true,
                    ],
                    [
                        'active' => false,
                    ],
                ],
                'subConstructor' => [
                    'active' => false,
                ],
                'id' => 7331,
                'amount' => 111.11,
            ],
        ];

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->array($array, BaseConstructor::class);

        $expected01 = new BaseConstructor(
            222.22,
            'approach',
            1337,
            [
                'hello',
                'world',
            ],
            new SubConstructor(true),
            [
                new SubConstructor(true),
                new SubConstructor(false),
            ],
        );
        $expected02 = new BaseConstructor(
            111.11,
            'approach',
            7331,
            [
                'hello',
                'next',
                'world',
            ],
            new SubConstructor(false),
            [
                new SubConstructor(true),
                new SubConstructor(false),
            ],
        );

        $this->assertIsArray($return);
        $this->assertInstanceOf(BaseConstructor::class, $return[0]);
        $this->assertEquals([$expected01, $expected02], $return);
    }

    public function testSharedConstructorSortedData(): void
    {
        $array = [
            'property' => 'value',
            'propertyTrait' => 'traitValue',
        ];

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->array($array, SharedSubSetter::class);

        $expected = new SharedSubSetter('value', null, 'traitValue');

        $this->assertInstanceOf(SharedSubSetter::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testSharedConstructorUnsortedData(): void
    {
        $array = [
            'propertyTrait' => 'traitValue',
            'property' => 'value',
        ];

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->array($array, SharedSubSetter::class);

        $expected = new SharedSubSetter('value', null, 'traitValue');

        $this->assertInstanceOf(SharedSubSetter::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testSharedSetterSortedData(): void
    {
        $array = [
            'property' => 'value',
            'propertyTrait' => 'traitValue',
        ];

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->array($array, SharedSubSetter::class);

        $expected = new SharedSubSetter('value', null, 'traitValue');

        $this->assertInstanceOf(SharedSubSetter::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testSharedSetterUnsortedData(): void
    {
        $array = [
            'propertyTrait' => 'traitValue',
            'property' => 'value',
        ];

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->array($array, SharedSubSetter::class);

        $expected = new SharedSubSetter('value', null, 'traitValue');

        $this->assertInstanceOf(SharedSubSetter::class, $return);
        $this->assertEquals($expected, $return);
    }
}
