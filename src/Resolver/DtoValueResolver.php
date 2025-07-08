<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use Wundii\DataMapper\Interface\ValueDtoInterface;

final readonly class DtoValueResolver
{
    public function resolve(ValueDtoInterface $valueDto): mixed
    {
        return $valueDto->getValue();
    }
}
