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
use Wundii\DataMapper\Enum\SourceTypeEnum;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Exception\DataMapperInvalidArgumentException;

class YamlApproachBasicTest extends TestCase
{
    public function testExceptionWrongSourceData(): void
    {
        $dataMapper = new DataMapper();
        $this->expectException(DataMapperException::class);
        $this->expectExceptionMessage('The Yaml source is not a string');
        $dataMapper->map(SourceTypeEnum::YAML, [], BaseSetter::class);
    }

    public function testExceptionInvalidSourceData(): void
    {
        $dataMapper = new DataMapper();
        $this->expectException(DataMapperInvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid Yaml decode return');
        $dataMapper->map(SourceTypeEnum::YAML, '/', BaseSetter::class);
    }

    public function testConstructorDefault(): void
    {
        $file = __DIR__ . '/YamlFiles/ApproachBasicConstructor.yaml';

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->yaml(file_get_contents($file), BaseConstructor::class);

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
        $file = __DIR__ . '/YamlFiles/ApproachBasicProperty.yaml';

        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->yaml(file_get_contents($file), BaseProperty::class);

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
        $file = __DIR__ . '/YamlFiles/ApproachBasicSetter.yaml';

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->yaml(file_get_contents($file), BaseSetter::class);

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
        $file = __DIR__ . '/YamlFiles/TypeNull01.yaml';

        $this->expectExceptionMessage('Invalid object from YamlResolver, could not create an instance of Integration\Objects\ApproachBasic\BaseSetter');
        $dataMapper = new DataMapper();
        $dataMapper->yaml(file_get_contents($file), BaseSetter::class, ['string']);
    }

    public function testSetterWithoutRightData(): void
    {
        $file = __DIR__ . '/YamlFiles/TypeNull01.yaml';

        $dataMapper = new DataMapper();
        $return = $dataMapper->yaml(file_get_contents($file), BaseSetter::class, ['string'], forceInstance: true);

        $expected = new BaseSetter();

        $this->assertInstanceOf(BaseSetter::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testSetterIncomplete(): void
    {
        $file = __DIR__ . '/YamlFiles/ApproachBasicSetterIncomplete.yaml';

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->yaml(file_get_contents($file), BaseSetterWithConstructor::class);

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
        $file = __DIR__ . '/YamlFiles/ApproachBasicSetterIncomplete.yaml';

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->yaml(file_get_contents($file), BaseSetterCustomMethod::class);

        $expected = new BaseSetterCustomMethod('approach');

        $this->assertInstanceOf(BaseSetterCustomMethod::class, $return);
        $this->assertEquals($expected, $return);
        $this->assertSame([], $return->getSubSetters());
    }

    public function testSetterWithFailSubSetters(): void
    {
        $file = __DIR__ . '/YamlFiles/ApproachBasicSetterFail.yaml';

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->yaml(file_get_contents($file), BaseSetter::class);

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
        $file = __DIR__ . '/YamlFiles/ApproachBasicSetterWithWrongTypeInSubSettersCreateInstanceWithoutConstructor.yaml';

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->yaml(file_get_contents($file), BaseSetter::class);

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

    // public function testBenchmark(): void
    // {
    //     $file = __DIR__ . '/YamlFiles/ApproachBasicSetter.yaml';
    //
    //     $dataConfig = new DataConfig(ApproachEnum::SETTER);
    //     $dataMapper = new DataMapper();
    //     $dataMapper->setDataConfig($dataConfig);
    //
    //     $subSetter01 = new SubSetter();
    //     $subSetter01->setActive(true);
    //     $subsetter02 = new SubSetter();
    //     $subsetter02->setActive(true);
    //     $subSetter03 = new SubSetter();
    //     $subSetter03->setActive(false);
    //
    //     $expected = new BaseSetter();
    //     $expected->setAmount(222.22);
    //     $expected->setName('approach');
    //     $expected->setId(1337);
    //     $expected->setMyStrings([
    //         'hello',
    //         'world',
    //     ]);
    //     $expected->setSubSetter($subSetter01);
    //     $expected->setSubSetters([
    //         $subsetter02,
    //         $subSetter03,
    //     ]);
    //
    //     $fileContent = file_get_contents($file);
    //
    //     for ($i = 0; $i < 10000; $i++) {
    //         $return = $dataMapper->yaml($fileContent, BaseSetter::class);
    //         $this->assertInstanceOf(BaseSetter::class, $return);
    //         $this->assertEquals($expected, $return);
    //     }
    // }

    public function testMix(): void
    {
        $file = __DIR__ . '/YamlFiles/ApproachBasicMix.yaml';

        $dataMapper = new DataMapper();
        $return = $dataMapper->yaml(file_get_contents($file), BaseMix::class);

        $expected = new BaseMix(222.22, 'approach');

        $this->assertInstanceOf(BaseMix::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testMixWithCustomRootElementTree(): void
    {
        $file = __DIR__ . '/YamlFiles/ApproachBasicMixCustomRootElementTree.yaml';

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->yaml(file_get_contents($file), BaseMix::class, ['result']);

        $expected = new BaseMix(222.22, 'approach');

        $this->assertInstanceOf(BaseMix::class, $return);
        $this->assertEquals($expected, $return);

        $return = $dataMapper->yaml(file_get_contents($file), BaseMix::class, ['RESULT']);

        $expected = new BaseMix(222.22, 'approach');

        $this->assertInstanceOf(BaseMix::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testMixWithIncorrectCustomRootElementTreeWithoutForceInstance(): void
    {
        $file = __DIR__ . '/YamlFiles/ApproachBasicMixCustomRootElementTree.yaml';

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);

        $this->expectException(DataMapperException::class);
        $this->expectExceptionMessage('Root-Element "incorrect" not found in Yaml source data, you can use the forceInstance option to create an empty instance.');
        $dataMapper->yaml(file_get_contents($file), BaseMix::class, ['incorrect']);
    }

    public function testMixWithIncorrectCustomRootElementTreeWithForceInstance(): void
    {
        $file = __DIR__ . '/YamlFiles/TypeString.yaml';

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);

        $return = $dataMapper->yaml(file_get_contents($file), BaseMix::class, ['incorrect'], true);

        $this->assertInstanceOf(BaseMix::class, $return);
    }

    public function testPrivateProperty(): void
    {
        $file = __DIR__ . '/YamlFiles/ApproachBasicMix.yaml';

        $dataConfig = new DataConfig(
            ApproachEnum::PROPERTY,
            AccessibleEnum::PRIVATE,
        );
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->yaml(file_get_contents($file), PrivateProperty::class);

        $this->assertInstanceOf(PrivateProperty::class, $return);
        $this->assertEquals(222.22, $return->getAmount());
        $this->assertEquals('approach', $return->getName());
    }

    public function testPrivateSetter(): void
    {
        $file = __DIR__ . '/YamlFiles/ApproachBasicMix.yaml';

        $dataConfig = new DataConfig(
            ApproachEnum::SETTER,
            AccessibleEnum::PRIVATE,
        );
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->yaml(file_get_contents($file), PrivateSetter::class);

        $this->assertInstanceOf(PrivateSetter::class, $return);
        $this->assertEquals(222.22, $return->getAmount());
        $this->assertEquals('approach', $return->getName());
    }
}
