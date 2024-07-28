<?php

declare(strict_types=1);

namespace Integration\Objects\Types;

final class TypeArray
{
    /**
     * @param string[] $strings
     * @param int[] $ints
     * @param float[] $floats
     */
    public function __construct(
        public array $strings,
        public array $ints,
        public array $floats,
    ) {
    }
}
