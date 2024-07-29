<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Elements;

use Wundii\DataMapper\Interface\ElementDataInterface;

final readonly class DataBool implements ElementDataInterface
{
    public function __construct(
        private bool|int|string $value,
        private ?string $destination = null,
    ) {
    }

    public function __toString(): string
    {
        return $this->value ? 'true' : 'false';
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function getValue(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_BOOL);
    }
}
