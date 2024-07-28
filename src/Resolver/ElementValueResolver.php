<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use Exception;
use InvalidArgumentException;
use Wundii\DataMapper\Interface\ElementArrayInterface;
use Wundii\DataMapper\Interface\ElementDataInterface;
use Wundii\DataMapper\Interface\ElementObjectInterface;

final readonly class ElementValueResolver
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
