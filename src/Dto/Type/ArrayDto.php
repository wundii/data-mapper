<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto\Type;

use Wundii\DataMapper\Interface\ArrayDtoInterface;
use Wundii\DataMapper\Interface\TypeDtoInterface;

final readonly class ArrayDto implements ArrayDtoInterface
{
    /**
     * @param TypeDtoInterface[] $value
     */
    public function __construct(
        private array $value,
        private ?string $destination = null,
    ) {
    }

    public function __toString(): string
    {
        $value = array_slice($this->value, 0, 3);
        return implode(', ', $value);
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    /**
     * @return TypeDtoInterface[]
     */
    public function getValue(): array
    {
        return $this->value;
    }
}
