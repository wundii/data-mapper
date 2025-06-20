<?php

declare(strict_types=1);

namespace Integration;

use Integration\Objects\ApproachBasic\AttributeTargetConstructor;
use Integration\Objects\ApproachBasic\AttributeTargetProperty;
use Integration\Objects\ApproachBasic\AttributeTargetSetter;
use Integration\Objects\ApproachBasic\SubConstructor;
use Integration\Objects\ApproachBasic\SubProperty;
use Integration\Objects\ApproachBasic\SubSetter;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;

class ArrayApproachAttributeTargetTest extends TestCase
{
    public function testConstructorDefault(): void
    {
        $array = [
            'costs' => 222.22,
            'title' => 'approach',
            'primaryId' => 1337,
            'strings' => [
                'hello',
                'world',
            ],
            'subConst' => [
                'active' => true,
            ],
            'subConsts' => [
                [
                    'active' => true,
                ],
                [
                    'active' => false,
                ],
            ],
        ];

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);

        $return = $dataMapper->array($array, AttributeTargetConstructor::class);

        $expected = new AttributeTargetConstructor(
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

        $this->assertInstanceOf(AttributeTargetConstructor::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testProperty(): void
    {
        $array = [
            'costs' => 222.22,
            'title' => 'approach',
            'primaryId' => 1337,
            'strings' => [
                'hello',
                'world',
            ],
            'subProp' => [
                'active' => true,
            ],
            'subProps' => [
                [
                    'active' => true,
                ],
                [
                    'active' => false,
                ],
            ],
        ];

        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->array($array, AttributeTargetProperty::class);

        $subProperty01 = new SubProperty();
        $subProperty01->active = true;
        $subProperty02 = new SubProperty();
        $subProperty02->active = true;
        $subProperty03 = new SubProperty();
        $subProperty03->active = false;

        $expected = new AttributeTargetProperty();
        $expected->costs = 222.22;
        $expected->title = 'approach';
        $expected->primaryId = 1337;
        $expected->strings = [
            'hello',
            'world',
        ];
        $expected->subProp = $subProperty01;
        $expected->subProps = [
            $subProperty02,
            $subProperty03,
        ];

        $this->assertInstanceOf(AttributeTargetProperty::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testSetter(): void
    {
        $array = [
            'costs' => 222.22,
            'title' => 'approach',
            'primaryId' => 1337,
            'strings' => [
                'hello',
                'world',
            ],
            'subSet' => [
                'active' => true,
            ],
            'subSets' => [
                [
                    'active' => true,
                ],
                [
                    'active' => false,
                ],
            ],
        ];

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->array($array, AttributeTargetSetter::class);

        $subSetter01 = new SubSetter();
        $subSetter01->setActive(true);
        $subsetter02 = new SubSetter();
        $subsetter02->setActive(true);
        $subSetter03 = new SubSetter();
        $subSetter03->setActive(false);

        $expected = new AttributeTargetSetter();
        $expected->setCosts(222.22);
        $expected->setTitle('approach');
        $expected->setPrimaryId(1337);
        $expected->setStrings([
            'hello',
            'world',
        ]);
        $expected->setSubSet($subSetter01);
        $expected->setSubSets([
            $subsetter02,
            $subSetter03,
        ]);

        $this->assertInstanceOf(AttributeTargetSetter::class, $return);
        $this->assertEquals($expected, $return);
    }
}
