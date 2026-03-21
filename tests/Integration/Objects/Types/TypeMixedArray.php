<?php

declare(strict_types=1);

namespace Integration\Objects\Types;

final class TypeMixedArray
{
    /**
     * @param array<mixed> $data
     */
    public function __construct(
        public string $name,
        public array $data,
    ) {
    }
}
