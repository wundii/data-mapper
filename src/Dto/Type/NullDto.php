<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto\Type;

use Wundii\DataMapper\Interface\ValueDtoInterface;

final readonly class NullDto implements ValueDtoInterface
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
