<?php

declare(strict_types=1);

namespace Integration\Objects\Serialize;

use Integration\Objects\Types\TypeString;

final readonly class Getter
{
    /**
     * @param SubConstructorProperty[] $subConstructors
     */
    public function __construct(
        private float $amount,
        private string $name,
        private TypeString $typeString,
        private array $myStrings = [],
        private array $subConstructors = [],
    ) {
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getTypeString(): TypeString
    {
        return $this->typeString;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getMyStrings(): array
    {
        return $this->myStrings;
    }

    /**
     * @return SubConstructorProperty[]
     */
    public function getSubConstructors(): array
    {
        return $this->subConstructors;
    }
}
