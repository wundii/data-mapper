<?php

declare(strict_types=1);

namespace DataMapper\Tests\MockClasses;

final class RootClassSetters implements RootClassInterface
{
    private string $name;

    private ?int $id = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }
}
