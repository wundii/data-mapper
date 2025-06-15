<?php

declare(strict_types=1);

namespace Integration;

use Integration\Objects\ApproachBasic\BaseConstructor;
use Integration\Objects\Serialize\ToArray;
use Integration\Objects\Types\TypeString;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;

class ObjectBasicTest extends TestCase
{
    public function testConstructorDefault(): void
    {
        $typeString = new TypeString('foo');
        $object = new ToArray(1.23, 'test', $typeString);

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);

        $return = $dataMapper->object($object, BaseConstructor::class);

        $expected = new BaseConstructor(1.23, 'test');

        $this->assertInstanceOf(BaseConstructor::class, $return);
        $this->assertEquals($expected, $return);
    }
}
