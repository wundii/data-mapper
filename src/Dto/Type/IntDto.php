<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto\Type;

use Wundii\DataMapper\Interface\ValueDtoInterface;

final readonly class IntDto implements ValueDtoInterface
{
    public function __construct(
        private int|float|string $value,
        private ?string $destination = null,
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function getValue(): int
    {
        return filter_var($this->value, FILTER_VALIDATE_INT) ?: (int) $this->value;
    }
}
