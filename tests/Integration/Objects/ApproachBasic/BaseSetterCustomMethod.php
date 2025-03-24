<?php

declare(strict_types=1);

namespace Integration\Objects\ApproachBasic;

final class BaseSetterCustomMethod
{
    /**
     * @param SubSetter[] $subSetters
     */
    public function __construct(
        private string $name,
        private array $subSetters = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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
        $this->subSetters[] = array_merge($this->subSetters, $subSetters);
    }
}
