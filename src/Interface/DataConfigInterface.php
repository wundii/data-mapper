<?php

declare(strict_types=1);

namespace DataMapper\Interface;

use DataMapper\Enum\AccessibleEnum;
use DataMapper\Enum\ApproachEnum;

interface DataConfigInterface
{
    public function getApproach(): ApproachEnum;

    public function getAccessible(): AccessibleEnum;

    /**
     * @return string[]
     */
    public function getClassMap(): array;

    public function mapClassName(string $objectName): string;
}
