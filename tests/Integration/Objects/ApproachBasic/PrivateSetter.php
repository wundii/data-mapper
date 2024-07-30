<?php

declare(strict_types=1);

namespace Integration\Objects\ApproachBasic;

final class PrivateSetter
{
    private float $amount;

    private string $name;

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getName(): string
    {
        return $this->name;
    }

    private function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    private function setName(string $name): void
    {
        $this->name = $name;
    }
}
