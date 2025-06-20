<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Interface;

use Stringable;

interface TypeDtoInterface extends Stringable
{
    public function getDestination(): ?string;
}
