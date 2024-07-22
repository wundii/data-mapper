<?php

declare(strict_types=1);

namespace DataMapper\Enum;

use DataMapper\DataConfig;
use DataMapper\Interface\SourceDataInterface;
use DataMapper\SourceData\JsonSourceData;
use DataMapper\SourceData\XmlSourceData;

enum SourceTypeEnum
{
    case JSON;
    case XML;

    public static function SourceDataInstance(
        SourceTypeEnum $sourceTypeEnum,
        DataConfig $dataConfig,
        string $source,
        string|object $object,
    ): SourceDataInterface {
        return match ($sourceTypeEnum) {
            SourceTypeEnum::JSON => new JsonSourceData($dataConfig, $source, $object),
            SourceTypeEnum::XML => new XmlSourceData($dataConfig, $source, $object),
        };
    }
}
