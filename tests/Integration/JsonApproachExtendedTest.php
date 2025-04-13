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

class JsonApproachExtendedTest extends TestCase
{
    public function testConstructorDefault(): void
    {
        $file = __DIR__ . '/JsonFiles/ApproachExtendedConstructor.json';

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->json(file_get_contents($file), BaseConstructor::class);

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
}
