<?php

declare(strict_types=1);

namespace Integration;

use Integration\Objects\ApproachBasic\BaseConstructor;
use Integration\Objects\ApproachBasic\BaseMix;
use Integration\Objects\ApproachBasic\BaseProperty;
use Integration\Objects\ApproachBasic\BaseSetter;
use Integration\Objects\ApproachBasic\BaseSetterCustomMethod;
use Integration\Objects\ApproachBasic\BaseSetterWithConstructor;
use Integration\Objects\ApproachBasic\PrivateProperty;
use Integration\Objects\ApproachBasic\PrivateSetter;
use Integration\Objects\ApproachBasic\SubConstructor;
use Integration\Objects\ApproachBasic\SubProperty;
use Integration\Objects\ApproachBasic\SubSetter;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Exception\DataMapperException;

class ArrayApproachBasicTest extends TestCase
{
    public function testConstructorDefault(): void
    {
        $array = [
            'amount' => 222.22,
            'name' => 'approach',
            'id' => 1337,
            'myStrings' => [
                'hello',
                'world',
            ],
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
        ];

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);

        $return = $dataMapper->array($array, BaseConstructor::class);

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
        $array = [
            'amount' => 222.22,
            'name' => 'approach',
            'id' => 1337,
            'myStrings' => [
                'hello',
                'world',
            ],
            'subProperty' => [
                'active' => true,
            ],
            'subProperties' => [
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
        $return = $dataMapper->array($array, BaseProperty::class);

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
        $array = [
            'amount' => 222.22,
            'name' => 'approach',
            'id' => 1337,
            'myStrings' => [
                'hello',
                'world',
            ],
            'subSetter' => [
                'active' => true,
            ],
            'subSetters' => [
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
        $return = $dataMapper->array($array, BaseSetter::class);

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

    public function testSetterWithoutRightDataException(): void
    {
        $array = [
            'string' => null,
        ];

        $this->expectExceptionMessage('Invalid object from ArrayResolver, could not create an instance of Integration\Objects\ApproachBasic\BaseSetter');
        $dataMapper = new DataMapper();
        $dataMapper->array($array, BaseSetter::class, ['string']);
    }

    public function testSetterWithoutRightData(): void
    {
        $array = [
            'string' => null,
        ];

        $dataMapper = new DataMapper();
        $return = $dataMapper->array($array, BaseSetter::class, ['string'], forceInstance: true);

        $expected = new BaseSetter();

        $this->assertInstanceOf(BaseSetter::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testSetterIncomplete(): void
    {
        $array = [
            'amount' => 222.22,
            'name' => 'approach',
            'id' => 1337,
            'myStrings' => [
                'hello',
                'world',
            ],
        ];

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->array($array, BaseSetterWithConstructor::class);

        $expected = new BaseSetterWithConstructor(222.22, 'approach', 1337);
        $expected->setMyStrings([
            'hello',
            'world',
        ]);
        $expected->setSubSetter(null);
        $expected->setSubSetters([]);

        $this->assertInstanceOf(BaseSetterWithConstructor::class, $return);
        $this->assertEquals($expected, $return);
        $this->assertSame(null, $return->getSubSetter());
        $this->assertSame([], $return->getSubSetters());
    }

    public function testSetterCustomMethod(): void
    {
        $array = [
            'amount' => 222.22,
            'name' => 'approach',
            'id' => 1337,
            'myStrings' => [
                'hello',
                'world',
            ],
        ];

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->array($array, BaseSetterCustomMethod::class);

        $expected = new BaseSetterCustomMethod('approach');

        $this->assertInstanceOf(BaseSetterCustomMethod::class, $return);
        $this->assertEquals($expected, $return);
        $this->assertSame([], $return->getSubSetters());
    }

    public function testSetterWithFailSubSetters(): void
    {
        $array = [
            'amount' => 222.22,
            'name' => 'approach',
            'id' => 1337,
            'myStrings' => [
                'hello',
                'world',
            ],
            'subSetter' => [
                'active' => true,
            ],
            'subSetters' => [
                [
                    'fail' => true,
                ],
            ],
        ];

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->array($array, BaseSetter::class);

        $subSetter01 = new SubSetter();
        $subSetter01->setActive(true);

        $expected = new BaseSetter();
        $expected->setAmount(222.22);
        $expected->setName('approach');
        $expected->setId(1337);
        $expected->setMyStrings([
            'hello',
            'world',
        ]);
        $expected->setSubSetter($subSetter01);
        $expected->setSubSetters([]);

        $this->assertInstanceOf(BaseSetter::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testSetterWithWrongTypeInSubSettersCreateInstanceWithoutConstructor(): void
    {
        $array = [
            'amount' => 222.22,
            'name' => 'approach',
            'id' => 1337,
            'myStrings' => [
                'hello',
                'world',
            ],
            'subSetter' => [
                'active' => true,
            ],
            'subSetters' => [
                'fail',
            ],
        ];

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->array($array, BaseSetter::class);

        $subSetter01 = new SubSetter();
        $subSetter01->setActive(true);
        $subSetter02 = new SubSetter();

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
            $subSetter02,
        ]);

        $this->assertInstanceOf(BaseSetter::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testMix(): void
    {
        $array = [
            'amount' => 222.22,
            'name' => 'approach',
        ];

        $dataMapper = new DataMapper();
        $return = $dataMapper->array($array, BaseMix::class);

        $expected = new BaseMix(222.22, 'approach');

        $this->assertInstanceOf(BaseMix::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testMixWithCustomRootElementTree(): void
    {
        $array = [
            'result' => [
                'amount' => 222.22,
                'name' => 'approach',
            ],
        ];

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->array($array, BaseMix::class, ['result']);

        $expected = new BaseMix(222.22, 'approach');

        $this->assertInstanceOf(BaseMix::class, $return);
        $this->assertEquals($expected, $return);

        $return = $dataMapper->array($array, BaseMix::class, ['RESULT']);

        $expected = new BaseMix(222.22, 'approach');

        $this->assertInstanceOf(BaseMix::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testMixWithIncorrectCustomRootElementTreeWithoutForceInstance(): void
    {
        $array = [
            'result' => [
                'amount' => 222.22,
                'name' => 'approach',
            ],
        ];

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);

        $this->expectException(DataMapperException::class);
        $this->expectExceptionMessage('Root-Element "incorrect" not found in Array source data, you can use the forceInstance option to create an empty instance.');
        $dataMapper->array($array, BaseMix::class, ['incorrect']);
    }

    public function testMixWithIncorrectCustomRootElementTreeWithForceInstance(): void
    {
        $array = [
            'string' => 'Nostromo',
        ];

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);

        $return = $dataMapper->array($array, BaseMix::class, ['incorrect'], true);

        $this->assertInstanceOf(BaseMix::class, $return);
    }

    public function testPrivateProperty(): void
    {
        $array = [
            'amount' => 222.22,
            'name' => 'approach',
        ];

        $dataConfig = new DataConfig(
            ApproachEnum::PROPERTY,
            AccessibleEnum::PRIVATE,
        );
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->array($array, PrivateProperty::class);

        $this->assertInstanceOf(PrivateProperty::class, $return);
        $this->assertEquals(222.22, $return->getAmount());
        $this->assertEquals('approach', $return->getName());
    }

    public function testPrivateSetter(): void
    {
        $array = [
            'amount' => 222.22,
            'name' => 'approach',
        ];

        $dataConfig = new DataConfig(
            ApproachEnum::SETTER,
            AccessibleEnum::PRIVATE,
        );
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->array($array, PrivateSetter::class);

        $this->assertInstanceOf(PrivateSetter::class, $return);
        $this->assertEquals(222.22, $return->getAmount());
        $this->assertEquals('approach', $return->getName());
    }
}
