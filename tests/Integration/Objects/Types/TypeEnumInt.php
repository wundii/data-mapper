<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\Types;

enum TypeEnumInt: int
{
    case BERLIN = 1;
    case TOKYO = 2;
    case LONDON = 3;
}
