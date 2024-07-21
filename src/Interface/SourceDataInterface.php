<?php

declare(strict_types=1);

namespace DataMapper\Interface;

use DataMapper\DataConfig;

interface SourceDataInterface
{
    public function __construct(DataConfig $dataConfig, string $source, string $objectName);

    public function coreLogic(): ObjectElementInterface;

    public function resolve(): object;
}
