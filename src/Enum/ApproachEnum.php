<?php

declare(strict_types=1);

namespace DataMapper\Enum;

enum ApproachEnum
{
    case CONSTRUCTOR;
    case PROPERTY;
    case SETTER;
}
