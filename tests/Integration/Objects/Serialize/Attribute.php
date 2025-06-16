<?php

declare(strict_types=1);

namespace Integration\Objects\Serialize;

use Integration\Objects\Types\TypeString;
use Wundii\DataMapper\Attribute\SourceData;

final readonly class Attribute
{
    /**
     * @param SubConstructorProperty[] $constructors
     */
    public function __construct(
        #[SourceData('amount')]
        public float $total,
        private string $label,
        private TypeString $classString,
        private array $myTags = [],
        private array $constructors = [],
    ) {
    }

    #[SourceData('myString')]
    public function getClassString(): TypeString
    {
        return $this->classString;
    }

    #[SourceData('name')]
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string[]
     */
    #[SourceData('myStrings')]
    public function getMyTags(): array
    {
        return $this->myTags;
    }

    /**
     * @return SubConstructorProperty[]
     */
    #[SourceData('subConstructors')]
    public function getConstructors(): array
    {
        return $this->constructors;
    }
}
