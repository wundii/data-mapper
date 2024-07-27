<?php

declare(strict_types=1);

namespace DataMapper;

use DataMapper\Enum\SourceTypeEnum;
use DataMapper\Interface\DataConfigInterface;
use InvalidArgumentException;

readonly class DataMapper
{
    /**
     * @param mixed[] $source
     */
    public function array(
        array $source,
        string|object $object,
        null|DataConfigInterface $dataConfig = null,
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
        null|DataConfigInterface $dataConfig = null,
    ): object {
        return $this->map(SourceTypeEnum::JSON, $source, $object, $dataConfig);
    }

    public function xml(
        string $source,
        string|object $object,
        null|DataConfigInterface $dataConfig = null,
    ): object {
        return $this->map(SourceTypeEnum::XML, $source, $object, $dataConfig);
    }

    private function map(
        SourceTypeEnum $sourceTypeEnum,
        string $source,
        string|object $object,
        null|DataConfigInterface $dataConfig = null,
    ): object {
        if (! $dataConfig instanceof DataConfigInterface) {
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
