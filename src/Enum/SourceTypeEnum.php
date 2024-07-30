<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Enum;

use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\Interface\SourceDataInterface;
use Wundii\DataMapper\SourceData\JsonSourceData;
use Wundii\DataMapper\SourceData\XmlSourceData;

enum SourceTypeEnum
{
    case JSON;
    case XML;

    /**
     * @param string[] $rootElementTree
     */
    public static function sourceDataInstance(
        SourceTypeEnum $sourceTypeEnum,
        DataConfigInterface $dataConfig,
        string $source,
        string|object $object,
        array $rootElementTree = [],
    ): SourceDataInterface {
        return match ($sourceTypeEnum) {
            SourceTypeEnum::JSON => new JsonSourceData($dataConfig, $source, $object, $rootElementTree),
            SourceTypeEnum::XML => new XmlSourceData($dataConfig, $source, $object, $rootElementTree),
        };
    }
}
