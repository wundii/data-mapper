<?php

declare(strict_types=1);

namespace Unit\Enum;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\Enum\SourceTypeEnum;
use Wundii\DataMapper\SourceData\JsonSourceData;
use Wundii\DataMapper\SourceData\XmlSourceData;

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
