<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\Types;

use DataMapper\Tests\MockClasses\RootInterface;

final class TypeArray implements RootInterface
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
