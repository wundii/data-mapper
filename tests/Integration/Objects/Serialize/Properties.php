<?php

declare(strict_types=1);

namespace Integration\Objects\Serialize;

use Integration\Objects\Types\TypeString;

final readonly class Properties
{
    /**
     * @param string[] $myStrings
     * @param SubConstructorProperty[] $subConstructors
     */
    public function __construct(
        public float $amount,
        public string $name,
        public TypeString $typeString,
        public array $myStrings = [],
        public array $subConstructors = [],
    ) {
    }
}
