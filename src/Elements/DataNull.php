<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Elements;

use Wundii\DataMapper\Interface\ElementValueInterface;

final readonly class DataNull implements ElementValueInterface
{
    public function __construct(
        private ?string $destination = null,
    ) {
    }

    public function __toString(): string
    {
        return 'null';
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function getValue(): null
    {
        return null;
    }
}
