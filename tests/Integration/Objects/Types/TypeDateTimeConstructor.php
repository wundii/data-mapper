<?php

declare(strict_types=1);

namespace Integration\Objects\Types;

use DateTime;

final class TypeDateTimeConstructor
{
    public function __construct(
        public string $name,
        public DateTime $createdAt,
    ) {
    }
}
