<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration;

use DataMapper\DataConfig;
use DataMapper\DataMapper;
use DataMapper\Enum\ApproachEnum;
use DataMapper\Tests\MockClasses\ItemClassConstructor;
use DataMapper\Tests\MockClasses\RootClassConstructor;
use DataMapper\Tests\MockClasses\RootClassProperties;
use PHPUnit\Framework\TestCase;

class XmlMapperTest extends TestCase
{
    public function testXmlMapperConstruct(): void
    {
        $file = __DIR__ . '/Files/XmlMapperTest01.xml';

        $dataConfig = new DataConfig();
        $dataMapper = new DataMapper($dataConfig);
        $return = $dataMapper->xml(file_get_contents($file), RootClassConstructor::class);

        $expected = new RootClassConstructor(
            'constructor',
            new ItemClassConstructor(
                12.34,
                true,
            ),
            1,
            [
                'hello',
                'world',
            ],
        );

        $this->assertInstanceOf(RootClassConstructor::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testXmlMapperProperties(): void
    {
        $file = __DIR__ . '/Files/XmlMapperTest01.xml';

        $dataConfig = new DataConfig(
            ApproachEnum::PROPERTY,
        );
        $dataMapper = new DataMapper($dataConfig);
        $return = $dataMapper->xml(file_get_contents($file), RootClassProperties::class);

        $expected = new RootClassProperties();
        $expected->name = 'constructor';
        $expected->id = 1;

        $this->assertInstanceOf(RootClassProperties::class, $return);
        $this->assertEquals($expected, $return);
    }
}
