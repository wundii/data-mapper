<?php

declare(strict_types=1);

namespace DataMapper\Tests\MockClasses;

final readonly class MapperClassConstructor implements MapperClassInterface
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
