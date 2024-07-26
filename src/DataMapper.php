<?php

declare(strict_types=1);

namespace DataMapper;

use DataMapper\Enum\SourceTypeEnum;
use InvalidArgumentException;

if (PHP_VERSION_ID < 80300) {
    function json_validate(string $string): bool
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}

final readonly class DataMapper
{
    /**
     * @param mixed[] $source
     */
    public function array(
        array $source,
        string|object $object,
        null|DataConfig $dataConfig = null,
    ): object {
        $json = json_encode($source);

        if ($json === false) {
            throw new InvalidArgumentException('Could not encode the array to JSON');
        }

        return $this->map(SourceTypeEnum::JSON, $json, $object, $dataConfig);
    }

    public function json(
        string $source,
        string|object $object,
        null|DataConfig $dataConfig = null,
    ): object {
        if (! json_validate($source)) {
            throw new InvalidArgumentException('Invalid JSON string');
        }

        return $this->map(SourceTypeEnum::JSON, $source, $object, $dataConfig);
    }

    public function xml(
        string $source,
        string|object $object,
        null|DataConfig $dataConfig = null,
    ): object {
        return $this->map(SourceTypeEnum::XML, $source, $object, $dataConfig);
    }

    private function map(
        SourceTypeEnum $sourceTypeEnum,
        string $source,
        string|object $object,
        null|DataConfig $dataConfig = null,
    ): object {
        if (! $dataConfig instanceof DataConfig) {
            $dataConfig = new DataConfig();
        }

        $sourceData = SourceTypeEnum::sourceDataInstance(
            $sourceTypeEnum,
            $dataConfig,
            $source,
            $object,
        );

        return $sourceData->resolve();
    }
}
