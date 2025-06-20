<?php

declare(strict_types=1);

namespace Integration\Objects\ApproachBasic;

use Wundii\DataMapper\Attribute\TargetData;

final readonly class AttributeTargetConstructor
{
    /**
     * @param string[] $strings
     * @param SubConstructor[] $subConsts
     */
    public function __construct(
        #[TargetData('amount')]
        private float $costs,
        #[TargetData('name')]
        private string $title,
        #[TargetData('id')]
        private ?int $primaryId = null,
        #[TargetData('myStrings')]
        private array $strings = [],
        #[TargetData('subConstructor')]
        private ?SubConstructor $subConst = null,
        #[TargetData('subConstructors')]
        private array $subConsts = [],
    ) {
    }

    public function getCosts(): float
    {
        return $this->costs;
    }

    public function getPrimaryId(): ?int
    {
        return $this->primaryId;
    }

    /**
     * @return string[]
     */
    public function getStrings(): array
    {
        return $this->strings;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSubConst(): ?SubConstructor
    {
        return $this->subConst;
    }

    /**
     * @return SubConstructor[]
     */
    public function getSubConsts(): array
    {
        return $this->subConsts;
    }
}
