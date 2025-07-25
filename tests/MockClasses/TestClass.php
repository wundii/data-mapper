<?php

declare(strict_types=1);

namespace MockClasses;

use Wundii\DataMapper\Attribute\SourceData;
use Wundii\DataMapper\Attribute\TargetData;

class TestClass
{
    public function __construct(
        /**
         * @var string
         */
        private string $name,
        #[SourceData('test_age')]
        private int $age,
        private bool $active = true,
    ) {
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    #[TargetData('test_name')]
    private function getName(): string|bool
    {
        return $this->name;
    }
}
