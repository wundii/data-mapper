<?php

declare(strict_types=1);

namespace DataMapper\Interface;

use DataMapper\DataConfig;

interface SourceDataInterface
{
    public function __construct(DataConfig $dataConfig, string $source, string $objectName);

    public function executeConstructor(): object;

    public function executeProperty(): object;

    public function executeSetter(): object;
}
