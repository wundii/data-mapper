<?php

declare(strict_types=1);

namespace DataMapper\Tests\MockClasses;

final readonly class RootClassConstructor implements RootClassInterface
{
    /**
     * @PARAM ?int $id
     * @param string[] $data
     * @return ?int
     */
    public function __construct(
        private string $name,
        private ItemClassConstructor $item,
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

    public function getItem(): ItemClassConstructor
    {
        return $this->item;
    }
}
