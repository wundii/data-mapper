<?php

declare(strict_types=1);

namespace Integration\Objects\Types;

final class TypeDateTimeImmutable
{
    public function __construct(
        public string $name,
        public \DateTimeImmutable $createdAt,
    ) {
    }
}
