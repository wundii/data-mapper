<?php

declare(strict_types=1);

namespace Integration\Objects\Serialize;

use Integration\Objects\Types\TypeInt;
use Integration\Objects\Types\TypeString;

final readonly class ToArray
{
    /**
     * @param TypeInt[] $array
     */
    public function __construct(
        private float $amount,
        private string $name,
        private TypeString $typeString,
        private array $array = [],
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
     * @return TypeInt[]
     */
    public function getArray(): array
    {
        return $this->array;
    }

    /**
     * @return string[]
     */
    public function tosArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
