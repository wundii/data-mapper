<?php

declare(strict_types=1);

namespace Integration\Objects\ApproachBasic;

class SharedSetter
{
    use SharedSetterTrait;

    public function __construct(
        private string $property,
        private ?int $amount = null,
        ?string $propertyTrait = null,
    ) {
        $this->setData($propertyTrait);
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function setProperty(string $property): void
    {
        $this->property = $property;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount = null): void
    {
        $this->amount = $amount;
    }
}
