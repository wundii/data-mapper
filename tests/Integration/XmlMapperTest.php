<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration;

use DataMapper\DataConfig;
use DataMapper\DataMapper;
use DataMapper\Enum\ApproachEnum;
use DataMapper\Tests\MockClasses\MapperClassConstructor;
use DataMapper\Tests\MockClasses\MapperClassProperties;
use PHPUnit\Framework\TestCase;

class XmlMapperTest extends TestCase
{
    public function testXmlMapperConstruct(): void
    {
        $file = __DIR__ . '/Files/XmlMapperTest01.xml';

        $dataConfig = new DataConfig();
        $dataMapper = new DataMapper($dataConfig);
        $return = $dataMapper->xml(file_get_contents($file), MapperClassConstructor::class);

        $expected = new MapperClassConstructor(
            'constructor',
            1,
        );

        $this->assertInstanceOf(MapperClassConstructor::class, $return);
        $this->assertEquals($expected, $return);
    }

    public function testXmlMapperProperties(): void
    {
        $file = __DIR__ . '/Files/XmlMapperTest01.xml';

        $dataConfig = new DataConfig(
            ApproachEnum::PROPERTY,
        );
        $dataMapper = new DataMapper($dataConfig);
        $return = $dataMapper->xml(file_get_contents($file), MapperClassProperties::class);

        $expected = new MapperClassProperties();
        $expected->name = 'constructor';
        $expected->id = 1;

        $this->assertInstanceOf(MapperClassProperties::class, $return);
        $this->assertEquals($expected, $return);
    }
}
