<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\ElementArrayInterface;
use Wundii\DataMapper\Interface\ElementDataInterface;
use Wundii\DataMapper\Interface\ElementObjectInterface;

final readonly class ElementValueResolver
{
    public function resolve(ElementDataInterface $elementData): mixed
    {
        if ($elementData instanceof ElementObjectInterface) {
            throw DataMapperException::InvalidArgument('ObjectElementInterface not supported', (string) $elementData);
        }

        if ($elementData instanceof ElementArrayInterface) {
            throw DataMapperException::InvalidArgument('DataObject not supported', (string) $elementData);
        }

        return $elementData->getValue();
    }
}
