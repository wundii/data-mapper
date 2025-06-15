<?php

declare(strict_types=1);

namespace Integration\Objects\Serialize;

use Integration\Objects\Types\TypeInt;
use Integration\Objects\Types\TypeString;

final readonly class Properties
{
    /**
     * @param TypeInt[] $array
     */
    public function __construct(
        public float $amount,
        public string $name,
        public TypeString $typeString,
        public array $array = [],
    ) {
    }
}
