<?php

declare(strict_types=1);

namespace Integration;

use Integration\Objects\ApproachBasic\BaseConstructor;
use Integration\Objects\Serialize\Getter;
use Integration\Objects\Serialize\Properties;
use Integration\Objects\Types\TypeString;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;

class ObjectBasicTest extends TestCase
{
    public function testGetterBasic(): void
    {
        $typeString = new TypeString('foo');
        $object = new Getter(1.23, 'test', $typeString);

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);

        $return = $dataMapper->object($object, BaseConstructor::class);

        $expected = new BaseConstructor(1.23, 'test');

        $this->assertInstanceOf(BaseConstructor::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testPropertiesBasic(): void
    {
        $typeString = new TypeString('foo');
        $object = new Properties(1.23, 'test', $typeString);

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);

        $return = $dataMapper->object($object, BaseConstructor::class);

        $expected = new BaseConstructor(1.23, 'test');

        $this->assertInstanceOf(BaseConstructor::class, $return);
        $this->assertEquals($expected, $return);
    }
}
