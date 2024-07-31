<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use Wundii\DataMapper\Interface\ElementValueInterface;

final readonly class ElementValueResolver
{
    public function resolve(ElementValueInterface $elementValue): mixed
    {
        return $elementValue->getValue();
    }
}
