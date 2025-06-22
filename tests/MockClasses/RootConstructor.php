<?php

declare(strict_types=1);

namespace MockClasses;

use MockClasses\Sub\SubItemConstructor as SubItemConstructor;

#[ClassAttribute(value: 'RootProperties')]
final readonly class RootConstructor implements RootInterface
{
    /**
     * @param SubItemConstructor[] $data
     * @param string[] $myString
     */
    public function __construct(
        private string $name,
        private string $ort,
        private ?ItemConstructor $item = null,
        private ?int $id = null,
        private array $data = [],
        private array $myString = [],
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

    public function getMyString(): array
    {
        return $this->myString;
    }

    public function getOrt(): string
    {
        return $this->ort;
    }
}
