<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\Types;

use DataMapper\Tests\MockClasses\RootInterface;

final class TypeObjectArray implements RootInterface
{
    /**
     * @param TypeString[] $typeStrings
     */
    public function __construct(
        public array $typeStrings,
    ) {
    }
}
