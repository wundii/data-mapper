<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration;

use DataMapper\DataConfig;
use DataMapper\DataMapper;
use DataMapper\Enum\ApproachEnum;
use DataMapper\Tests\Integration\Objects\Abstract\Animal;
use DataMapper\Tests\Integration\Objects\Abstract\Dog;
use DataMapper\Tests\Integration\Objects\Abstract\Zoo;
use PHPUnit\Framework\TestCase;

class XmlMapperAbstractTest extends TestCase
{
    public function testAbstract(): void
    {
        $file = __DIR__ . '/XmlFiles/XmlMapperAbstractTest.xml';

        $dataConfig = new DataConfig(
            ApproachEnum::PROPERTY,
            classMap: [
                Animal::class => function (string $class) {
                    return Animal::getClassName($class);
                },
            ],
        );
        $dataMapper = new DataMapper($dataConfig);
        $return = $dataMapper->xml(file_get_contents($file), Zoo::class);

        $mila = new Dog();
        $mila->name = 'mila';

        $ayumi = new Dog();
        $ayumi->name = 'ayumi';

        $expected = new Zoo();
        $expected->animals = [
            $mila,
            $ayumi,
        ];

        $this->assertInstanceOf(Zoo::class, $return);
        $this->assertEquals($expected, $return);
    }
}
