<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\ApproachBasic;

final class BaseSetter
{
    private float $amount;

    private string $name;

    private ?int $id = null;

    /**
     * @var string[]
     */
    private array $myStrings = [];

    private ?SubSetter $subSetter;

    /**
     * @var SubSetter[]
     */
    private array $subSetters = [];

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string[]
     */
    public function getMyStrings(): array
    {
        return $this->myStrings;
    }

    /**
     * @param string[] $myStrings
     */
    public function setMyStrings(array $myStrings): void
    {
        $this->myStrings = $myStrings;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSubSetter(): ?SubSetter
    {
        return $this->subSetter;
    }

    public function setSubSetter(?SubSetter $subSetter): void
    {
        $this->subSetter = $subSetter;
    }

    /**
     * @return SubSetter[]
     */
    public function getSubSetters(): array
    {
        return $this->subSetters;
    }

    /**
     * @param SubSetter[] $subSetters
     */
    public function setSubSetters(array $subSetters): void
    {
        $this->subSetters = $subSetters;
    }
}