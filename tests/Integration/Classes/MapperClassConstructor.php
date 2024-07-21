<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Classes;

final readonly class MapperClassConstructor
{
    public function __construct(
        private string $name,
        private ?int $id = null,
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
}
