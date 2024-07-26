<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\ApproachBasic;

final readonly class BaseConstructor
{
    /**
     * @param string[] $myStrings
     * @param SubConstructor[] $subConstructors
     */
    public function __construct(
        private float           $amount,
        private string          $name,
        private ?int            $id = null,
        private array           $myStrings = [],
        private ?SubConstructor $subConstructor = null,
        private array           $subConstructors = [],
    ) {
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string[]
     */
    public function getMyStrings(): array
    {
        return $this->myStrings;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubConstructor(): ?SubConstructor
    {
        return $this->subConstructor;
    }

    /**
     * @return SubConstructor[]
     */
    public function getSubConstructors(): array
    {
        return $this->subConstructors;
    }
}
