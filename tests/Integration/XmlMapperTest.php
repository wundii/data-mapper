<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration;

use DataMapper\DataConfig;
use DataMapper\DataMapper;
use DataMapper\Tests\MockClasses\MapperClassConstructor;
use PHPUnit\Framework\TestCase;

class XmlMapperTest extends TestCase
{
    public function testXmlMapper(): void
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
}
