<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Interface;

interface AttributeInterface
{
    public function getName(): ?string;

    public function getValue(): ?string;
}
