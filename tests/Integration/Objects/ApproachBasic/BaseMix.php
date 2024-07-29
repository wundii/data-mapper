<?php

declare(strict_types=1);

namespace Integration\Objects\ApproachBasic;

final class BaseMix
{
    public function __construct(
        private float $amount,
        private string $name,
    ) {
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
