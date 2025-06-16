<?php

declare(strict_types=1);

namespace Integration;

use Integration\Objects\ApproachBasic\BaseConstructor;
use Integration\Objects\ApproachBasic\BaseSetter;
use Integration\Objects\ApproachBasic\SubConstructor;
use Integration\Objects\Serialize\Attribute;
use Integration\Objects\Serialize\Getter;
use Integration\Objects\Serialize\Properties;
use Integration\Objects\Serialize\SubConstructorProperty;
use Integration\Objects\Serialize\ToArray;
use Integration\Objects\Types\TypeString;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Enum\SourceTypeEnum;
use Wundii\DataMapper\Exception\DataMapperException;

class ObjectBasicTest extends TestCase
{
    public function testExceptionWrongSourceData(): void
    {
        $dataMapper = new DataMapper();
        $this->expectException(DataMapperException::class);
        $this->expectExceptionMessage('The Object source is not a array or object');
        $dataMapper->map(SourceTypeEnum::OBJECT, 'string', BaseSetter::class);
    }

    public function testToArrayBasic(): void
    {
        $object = new ToArray('Nostromo');

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);

        $return = $dataMapper->object($object, TypeString::class);

        $expected = new TypeString('Nostromo');

        $this->assertInstanceOf(TypeString::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testGetterBasic(): void
    {
        $object = new Getter(
            1.23,
            'test',
            new TypeString('Nostromo'),
            [
                'tag1',
                'tag2',
            ],
            [
                new SubConstructorProperty(true),
                new SubConstructorProperty(false),
            ],
        );

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);

        $return = $dataMapper->object($object, BaseConstructor::class);

        $expected = new BaseConstructor(
            1.23,
            'test',
            myStrings: [
                'tag1',
                'tag2',
            ],
            subConstructors: [
                new SubConstructor(true),
                new SubConstructor(false),
            ],
        );

        $this->assertInstanceOf(BaseConstructor::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testPropertiesBasic(): void
    {
        $object = new Properties(
            1.23,
            'test',
            new TypeString('Nostromo'),
            [
                'tag1',
                'tag2',
            ],
            [
                new SubConstructorProperty(true),
                new SubConstructorProperty(false),
            ],
        );

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);

        $return = $dataMapper->object($object, BaseConstructor::class);

        $expected = new BaseConstructor(
            1.23,
            'test',
            myStrings: [
                'tag1',
                'tag2',
            ],
            subConstructors: [
                new SubConstructor(true),
                new SubConstructor(false),
            ],
        );

        $this->assertInstanceOf(BaseConstructor::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testAttributeSourceDataBasic(): void
    {
        $object = new Attribute(
            1.23,
            'test',
            new TypeString('Nostromo'),
            [
                'tag1',
                'tag2',
            ],
            [
                new SubConstructorProperty(true),
                new SubConstructorProperty(false),
            ],
        );

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);

        $return = $dataMapper->object($object, BaseConstructor::class);

        $expected = new BaseConstructor(
            1.23,
            'test',
            myStrings: [
                'tag1',
                'tag2',
            ],
            subConstructors: [
                new SubConstructor(true),
                new SubConstructor(false),
            ],
        );

        $this->assertInstanceOf(BaseConstructor::class, $return);
        $this->assertEquals($expected, $return);
    }
}
