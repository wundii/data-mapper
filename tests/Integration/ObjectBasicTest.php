<?php

declare(strict_types=1);

namespace Integration;

use Integration\Objects\ApproachBasic\BaseConstructor;
use Integration\Objects\ApproachBasic\SubConstructor;
use Integration\Objects\Serialize\Getter;
use Integration\Objects\Serialize\Properties;
use Integration\Objects\Serialize\SubConstructorProperty;
use Integration\Objects\Serialize\ToArray;
use Integration\Objects\Types\TypeString;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;

class ObjectBasicTest extends TestCase
{
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
}
