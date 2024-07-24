<?php

declare(strict_types=1);

namespace DataMapper\Tests\MockClasses;

use DataMapper\Tests\MockClasses\Sub\SubItemConstructor as SubItemConstructor;

final readonly class RootConstructor implements RootInterface
{
    /**
     * @param SubItemConstructor[] $data
     */
    public function __construct(
        private string $name,
        private ItemConstructor $item,
        private ?int $id = null,
        private array $data = [],
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
