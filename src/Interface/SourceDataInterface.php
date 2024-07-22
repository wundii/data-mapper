<?php

declare(strict_types=1);

namespace DataMapper\Interface;

use DataMapper\DataConfig;

interface SourceDataInterface
{
    public function __construct(DataConfig $dataConfig, string $source, string|object $object);

    public function resolve(): object;
}
