<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Interface;

use Stringable;

interface ElementDataInterface extends Stringable
{
    public function getDestination(): ?string;

    public function getValue(): mixed;
}
