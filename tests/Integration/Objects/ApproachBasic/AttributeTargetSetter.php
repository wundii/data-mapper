<?php

declare(strict_types=1);

namespace Integration\Objects\ApproachBasic;

use Wundii\DataMapper\Attribute\TargetData;

final class AttributeTargetSetter
{
    private float $costs;

    private string $title;

    private ?int $primaryId = null;

    /**
     * @var string[]
     */
    private array $strings = [];

    private ?SubSetter $subSet;

    /**
     * @var SubSetter[]
     */
    private array $subSets = [];

    public function getCosts(): float
    {
        return $this->costs;
    }

    #[TargetData('amount')]
    public function setCosts(float $costs): void
    {
        $this->costs = $costs;
    }

    public function getPrimaryId(): ?int
    {
        return $this->primaryId;
    }

    #[TargetData('id')]
    public function setPrimaryId(?int $primaryId): void
    {
        $this->primaryId = $primaryId;
    }

    /**
     * @return string[]
     */
    public function getStrings(): array
    {
        return $this->strings;
    }

    /**
     * @param string[] $strings
     */
    #[TargetData('myStrings')]
    public function setStrings(array $strings): void
    {
        $this->strings = $strings;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    #[TargetData('name')]
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSubSet(): ?SubSetter
    {
        return $this->subSet;
    }

    #[TargetData('subSetter')]
    public function setSubSet(?SubSetter $subSet): void
    {
        $this->subSet = $subSet;
    }

    /**
     * @return SubSetter[]
     */
    public function getSubSets(): array
    {
        return $this->subSets;
    }

    /**
     * @param SubSetter[] $subSets
     */
    #[TargetData('subSetters')]
    public function setSubSets(array $subSets): void
    {
        $this->subSets = $subSets;
    }
}
