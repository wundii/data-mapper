<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Interface;

interface ElementValueInterface extends ElementDataInterface
{
    public function getValue(): mixed;
}
