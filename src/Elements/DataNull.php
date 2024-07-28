<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Elements;

use Wundii\DataMapper\Interface\ElementDataInterface;

final readonly class DataNull implements ElementDataInterface
{
    public function __construct(
        private ?string $destination = null,
    ) {
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
