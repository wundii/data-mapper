<?php

declare(strict_types=1);

namespace Integration;

use Integration\Objects\ApproachBasic\BaseConstructor;
use Integration\Objects\ApproachBasic\SubConstructor;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\ApproachEnum;

class XmlApproachExtendedTest extends TestCase
{
    public function testConstructorDefault(): void
    {
        $file = __DIR__ . '/XmlFiles/ApproachExtendedConstructor.xml';

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $dataMapper = new DataMapper();
        $dataMapper->setDataConfig($dataConfig);
        $return = $dataMapper->xml(file_get_contents($file), BaseConstructor::class);

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
        $this->assertEquals([$expected01,$expected02], $return);
    }
}
