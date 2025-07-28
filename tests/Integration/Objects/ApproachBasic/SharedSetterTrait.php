<?php

declare(strict_types=1);

namespace Integration\Objects\ApproachBasic;

trait SharedSetterTrait
{
    private ?string $propertyTrait;

    public function getPropertyTrait(): ?string
    {
        return $this->propertyTrait;
    }

    public function setPropertyTrait(?string $propertyTrait): void
    {
        $this->propertyTrait = $propertyTrait;
    }

    public function setData(?string $data): void
    {
        $this->propertyTrait = $data;
    }
}