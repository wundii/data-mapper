<?php

declare(strict_types=1);

namespace DataMapper\Tests\MockClasses;

use DataMapper\Tests\MockClasses\Sub\SubItemConstructor as SubItemConstructor;

final readonly class RootConstructor implements RootInterface
{
    /**
     * @param SubItemConstructor[] $data
     * @param string[] $mystring
     */
    public function __construct(
        private string $name,
        private string $ort,
        private ?ItemConstructor $item = null,
        private ?int $id = null,
        private array $data = [],
        private array $mystring = [],
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getItem(): ItemConstructor
    {
        return $this->item;
    }
}
