<?php

declare(strict_types=1);

namespace Unit\Enum;

use DataMapper\DataConfig;
use DataMapper\Enum\SourceTypeEnum;
use DataMapper\SourceData\JsonSourceData;
use DataMapper\SourceData\XmlSourceData;
use PHPUnit\Framework\TestCase;

class SourceTypeEnumTest extends TestCase
{
    public function testSourceDataInstanceJson(): void
    {
        $sourceDataInstance = SourceTypeEnum::sourceDataInstance(
            SourceTypeEnum::JSON,
            new DataConfig(),
            'source',
            'objectName',
        );
        $this->assertInstanceOf(JsonSourceData::class, $sourceDataInstance);

        $sourceDataInstance = SourceTypeEnum::sourceDataInstance(
            SourceTypeEnum::JSON,
            new DataConfig(),
            'source',
            new \stdClass(),
        );
        $this->assertInstanceOf(JsonSourceData::class, $sourceDataInstance);
    }

    public function testSourceDataInstanceXml(): void
    {
        $sourceDataInstance = SourceTypeEnum::sourceDataInstance(
            SourceTypeEnum::XML,
            new DataConfig(),
            'source',
            'objectName',
        );
        $this->assertInstanceOf(XmlSourceData::class, $sourceDataInstance);

        $sourceDataInstance = SourceTypeEnum::sourceDataInstance(
            SourceTypeEnum::XML,
            new DataConfig(),
            'source',
            new \stdClass(),
        );
        $this->assertInstanceOf(XmlSourceData::class, $sourceDataInstance);
    }
}
