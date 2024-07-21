<?php

declare(strict_types=1);

namespace DataMapper;

use DataMapper\Enum\AccessibleEnum;
use DataMapper\Enum\ApproachEnum;

final readonly class DataConfig
{
    public function __construct(
        private ApproachEnum $approachEnum = ApproachEnum::CONSTRUCTOR,
        private AccessibleEnum $accessibleEnum = AccessibleEnum::PUBLIC,
    ) {
    }

    public function getApproach(): ApproachEnum
    {
        return $this->approachEnum;
    }

    public function getAccessible(): AccessibleEnum
    {
        return $this->accessibleEnum;
    }
}
