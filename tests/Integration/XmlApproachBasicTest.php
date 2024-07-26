<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration;

use DataMapper\DataConfig;
use DataMapper\DataMapper;
use DataMapper\Enum\ApproachEnum;
use DataMapper\Tests\Integration\Objects\ApproachBasic\BaseConstructor;
use DataMapper\Tests\Integration\Objects\ApproachBasic\BaseProperty;
use DataMapper\Tests\Integration\Objects\ApproachBasic\BaseSetter;
use DataMapper\Tests\Integration\Objects\ApproachBasic\SubConstructor;
use DataMapper\Tests\Integration\Objects\ApproachBasic\SubProperty;
use DataMapper\Tests\Integration\Objects\ApproachBasic\SubSetter;
use PHPUnit\Framework\TestCase;

class XmlApproachBasicTest extends TestCase
{
    public function testConstructorDefault(): void
    {
        $file = __DIR__ . '/XmlFiles/ApproachBasicConstructor.xml';

        $dataConfig = new DataConfig();
        $dataMapper = new DataMapper($dataConfig);
        $return = $dataMapper->xml(file_get_contents($file), BaseConstructor::class);

        $expected = new BaseConstructor(
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

        $this->assertInstanceOf(BaseConstructor::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testProperty(): void
    {
        $file = __DIR__ . '/XmlFiles/ApproachBasicProperty.xml';

        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        $dataMapper = new DataMapper($dataConfig);
        $return = $dataMapper->xml(file_get_contents($file), BaseProperty::class);

        $subProperty01 = new SubProperty();
        $subProperty01->active = true;
        $subProperty02 = new SubProperty();
        $subProperty02->active = true;
        $subProperty03 = new SubProperty();
        $subProperty03->active = false;

        $expected = new BaseProperty();
        $expected->amount = 222.22;
        $expected->name = 'approach';
        $expected->id = 1337;
        $expected->myStrings = [
            'hello',
            'world',
        ];
        $expected->subProperty = $subProperty01;
        $expected->subProperties = [
            $subProperty02,
            $subProperty03,
        ];

        $this->assertInstanceOf(BaseProperty::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testSetter(): void
    {
        $file = __DIR__ . '/XmlFiles/ApproachBasicSetter.xml';

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper($dataConfig);
        $return = $dataMapper->xml(file_get_contents($file), BaseSetter::class);

        $subSetter01 = new SubSetter();
        $subSetter01->setActive(true);
        $subsetter02 = new SubSetter();
        $subsetter02->setActive(true);
        $subSetter03 = new SubSetter();
        $subSetter03->setActive(false);

        $expected = new BaseSetter();
        $expected->setAmount(222.22);
        $expected->setName('approach');
        $expected->setId(1337);
        $expected->setMyStrings([
            'hello',
            'world',
        ]);
        $expected->setSubSetter($subSetter01);
        $expected->setSubSetters([
            $subsetter02,
            $subSetter03,
        ]);

        $this->assertInstanceOf(BaseSetter::class, $return);
        $this->assertEquals($expected, $return);
    }
}