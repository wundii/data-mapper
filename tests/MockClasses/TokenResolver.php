<?php

declare(strict_types=1);

namespace MockClasses;

use MockClasses\ItemConstructor as CustomItemConstructor;
use MockClasses\Sub\SubItemConstructor as SubItemConstructor;

final readonly class TokenResolver implements RootInterface
{
    /**
     * @param SubItemConstructor[] $data1
     * @param ItemConstructor[] $data2
     * @param CustomItemConstructor[] $data3
     */
    public function __construct(
        private array $data1 = [],
        private array $data2 = [],
        private array $data3 = [],
    ) {
    }

    public function getData1(): array
    {
        return $this->data1;
    }

    public function getData2(): array
    {
        return $this->data2;
    }

    public function getData3(): array
    {
        return $this->data3;
    }
}
