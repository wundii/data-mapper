<?php

declare(strict_types=1);

namespace DataMapper;

use DataMapper\Enum\SourceTypeEnum;
use InvalidArgumentException;

final readonly class DataMapper
{
    public function __construct(
        private DataConfig $dataConfig,
    ) {
    }

    /**
     * @param mixed[] $source
     */
    public function array(
        array $source,
        string|object $object,
    ): object {
        $json = json_encode($source);

        if ($json === false) {
            throw new InvalidArgumentException('Could not encode the array to JSON');
        }

        return $this->map(SourceTypeEnum::JSON, $json, $object);
    }

    public function json(
        string $source,
        string|object $object,
    ): object {
        return $this->map(SourceTypeEnum::JSON, $source, $object);
    }

    public function xml(
        string $source,
        string|object $object,
    ): object {
        return $this->map(SourceTypeEnum::XML, $source, $object);
    }

    private function map(
        SourceTypeEnum $sourceTypeEnum,
        string $source,
        string|object $object,
    ): object {
        $sourceData = SourceTypeEnum::SourceDataInstance(
            $sourceTypeEnum,
            $this->dataConfig,
            $source,
            $object,
        );

        return $sourceData->resolve();
    }
}
