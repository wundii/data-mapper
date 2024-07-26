<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\ClassMapDirectValue;

use DataMapper\Tests\Integration\Objects\Types\TypeEnumInt;
use DataMapper\Tests\Integration\Objects\Types\TypeEnumString;

final class EnumBasic
{
    public TypeEnumInt $enumInt;

    public TypeEnumString $enumString;
}
