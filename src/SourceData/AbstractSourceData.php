<?php

declare(strict_types=1);

namespace DataMapper\SourceData;

use DataMapper\DataConfig;
use DataMapper\Interface\SourceDataInterface;

abstract class AbstractSourceData implements SourceDataInterface
{
    public function __construct(
        protected DataConfig $dataConfig,
        protected string $source,
        protected string $objectName,
    ) {
    }
}
