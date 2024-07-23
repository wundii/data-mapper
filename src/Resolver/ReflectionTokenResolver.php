<?php

declare(strict_types=1);

namespace DataMapper\Resolver;

use DataMapper\Interface\ElementArrayInterface;
use DataMapper\Interface\ElementDataInterface;
use DataMapper\Interface\ElementObjectInterface;
use Exception;
use InvalidArgumentException;

final readonly class ReflectionTokenResolver
{
    /**
     * @throws Exception
     */
    public function resolve(ElementDataInterface $elementData): mixed
    {
        if ($elementData instanceof ElementObjectInterface) {
            throw new InvalidArgumentException('ObjectElementInterface not supported');
        }

        if ($elementData instanceof ElementArrayInterface) {
            throw new InvalidArgumentException('DataObject not supported');
        }

        return $elementData->getValue();
    }
}
