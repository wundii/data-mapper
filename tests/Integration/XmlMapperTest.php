<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration;

use DataMapper\DataConfig;
use DataMapper\DataMapper;
use DataMapper\Enum\ApproachEnum;
use DataMapper\Tests\MockClasses\ItemConstructor;
use DataMapper\Tests\MockClasses\RootConstructor;
use DataMapper\Tests\MockClasses\RootProperties;
use DataMapper\Tests\MockClasses\RootSetters;
use DataMapper\Tests\MockClasses\Sub\SubItemConstructor;
use PHPUnit\Framework\TestCase;

class XmlMapperTest extends TestCase
{
    public function testXmlMapperConstruct(): void
    {
        $file = __DIR__ . '/Files/XmlMapperTest01.xml';

        $dataConfig = new DataConfig();
        $dataMapper = new DataMapper($dataConfig);
        $return = $dataMapper->xml(file_get_contents($file), RootConstructor::class);

        $expected = new RootConstructor(
            'constructor',
            'soest',
            new ItemConstructor(
                12.34,
                false,
            ),
            1,
            [
                new SubItemConstructor('json'),
                new SubItemConstructor('xml'),
            ],
            [
                'hello',
                'world',
            ],
        );

        $this->assertInstanceOf(RootConstructor::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testXmlMapperProperties(): void
    {
        $file = __DIR__ . '/Files/XmlMapperTest01.xml';

        $dataConfig = new DataConfig(
            ApproachEnum::PROPERTY,
        );
        $dataMapper = new DataMapper($dataConfig);
        $return = $dataMapper->xml(file_get_contents($file), RootProperties::class);

        $expected = new RootProperties();
        $expected->name = 'constructor';
        $expected->id = 1;
        $expected->mystring = [
            'hello',
            'world',
        ];

        $this->assertInstanceOf(RootProperties::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testXmlMapperSetters(): void
    {
        $file = __DIR__ . '/Files/XmlMapperTest01.xml';

        $dataConfig = new DataConfig(
            ApproachEnum::SETTER,
        );
        $dataMapper = new DataMapper($dataConfig);
        $return = $dataMapper->xml(file_get_contents($file), RootSetters::class);

        $expected = new RootSetters();
        $expected->setName('constructor');
        $expected->setId(1);
        $expected->setAmount(222.22);

        $this->assertInstanceOf(RootSetters::class, $return);
        $this->assertEquals($expected, $return);
    }
}
