<?php

declare(strict_types=1);

namespace DataMapper\Tests\Unit;

use DataMapper\DataConfig;
use DataMapper\Enum\SourceTypeEnum;
use DataMapper\SourceData\JsonSourceData;
use DataMapper\SourceData\XmlSourceData;
use PHPUnit\Framework\TestCase;

class SourceTypeEnumTest extends TestCase
{
    public function testSourceDataInstanceJson(): void
    {
        $sourceDataInstance = SourceTypeEnum::SourceDataInstance(
            SourceTypeEnum::JSON,
            new DataConfig(),
            'source',
            'objectName',
        );
        $this->assertInstanceOf(JsonSourceData::class, $sourceDataInstance);

        $sourceDataInstance = SourceTypeEnum::SourceDataInstance(
            SourceTypeEnum::JSON,
            new DataConfig(),
            'source',
            new \stdClass(),
        );
        $this->assertInstanceOf(JsonSourceData::class, $sourceDataInstance);
    }

    public function testSourceDataInstanceXml(): void
    {
        $sourceDataInstance = SourceTypeEnum::SourceDataInstance(
            SourceTypeEnum::XML,
            new DataConfig(),
            'source',
            'objectName',
        );
        $this->assertInstanceOf(XmlSourceData::class, $sourceDataInstance);

        $sourceDataInstance = SourceTypeEnum::SourceDataInstance(
            SourceTypeEnum::XML,
            new DataConfig(),
            'source',
            new \stdClass(),
        );
        $this->assertInstanceOf(XmlSourceData::class, $sourceDataInstance);
    }
}
