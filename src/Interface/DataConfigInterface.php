<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Interface;

use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ApproachEnum;

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
