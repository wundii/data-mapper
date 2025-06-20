<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Interface;

interface ValueDtoInterface extends TypeDtoInterface
{
    public function getValue(): mixed;
}
