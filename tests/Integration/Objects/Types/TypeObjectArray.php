<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\Types;

final class TypeObjectArray
{
    /**
     * @param TypeString[] $typeStrings
     */
    public function __construct(
        public array $typeStrings,
    ) {
    }
}
