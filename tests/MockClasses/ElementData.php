<?php

declare(strict_types=1);

namespace MockClasses;

use Wundii\DataMapper\Interface\ElementDataInterface;

class ElementData implements ElementDataInterface
{
    public function __toString(): string
    {
        return 'fail';
    }

    public function getDestination(): ?string
    {
        return null;
    }
}
