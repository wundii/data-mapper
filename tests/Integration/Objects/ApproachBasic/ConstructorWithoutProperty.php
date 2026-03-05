<?php

declare(strict_types=1);

namespace Integration\Objects\ApproachBasic;

final readonly class ConstructorWithoutProperty
{
    private string $collected;

    public function __construct(
        float $amount,
        string $name,
    ) {
        $this->collected = $amount . $name;
    }

    public function getCollected(): string
    {
        return $this->collected;
    }
}
