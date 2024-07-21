<?php

declare(strict_types=1);

namespace DataMapper;

use DataMapper\Enum\ApproachEnum;
use DataMapper\Enum\SourceTypeEnum;
use InvalidArgumentException;

final readonly class DataMapper
{
    public function __construct(
        private DataConfig $dataConfig,
    ) {
    }

    /**
     * @param array<mixed> $source
     */
    public function array(
        array $source,
        string $objectName,
    ): object {
        $json = json_encode($source);

        if ($json === false) {
            throw new InvalidArgumentException('Could not encode the array to JSON');
        }

        return $this->map(SourceTypeEnum::JSON, $json, $objectName);
    }

    public function json(
        string $source,
        string $objectName,
    ): object {
        return $this->map(SourceTypeEnum::JSON, $source, $objectName);
    }

    public function xml(
        string $source,
        string $objectName,
    ): object {
        return $this->map(SourceTypeEnum::XML, $source, $objectName);
    }

    private function map(
        SourceTypeEnum $sourceTypeEnum,
        string $source,
        string $objectName,
    ): object {
        $sourceData = SourceTypeEnum::SourceDataInstance(
            $sourceTypeEnum,
            $this->dataConfig,
            $source,
            $objectName,
        );

        return match ($this->dataConfig->getApproach()) {
            ApproachEnum::CONSTRUCTOR => $sourceData->executeConstructor(),
            ApproachEnum::PROPERTY => $sourceData->executeProperty(),
            ApproachEnum::SETTER => $sourceData->executeSetter(),
        };
    }
}
