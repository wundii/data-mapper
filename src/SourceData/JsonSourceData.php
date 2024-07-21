<?php

declare(strict_types=1);

namespace DataMapper\SourceData;

use DataMapper\DataConfig;

final class JsonSourceData extends AbstractSourceData
{
    public function __construct(
        private DataConfig $dataConfig, // @phpstan-ignore-line
        private string $source, // @phpstan-ignore-line
        private string $objectName,
    ) {
    }

    public function executeConstructor(): object
    {
        return $this->createInstanceFromString($this->objectName);
    }

    public function executeSetter(): object
    {
        return $this->createInstanceFromString($this->objectName);
    }

    public function executeProperty(): object
    {
        return $this->createInstanceFromString($this->objectName);
    }
}
