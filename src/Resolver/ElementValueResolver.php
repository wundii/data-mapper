<?php

declare(strict_types=1);

namespace DataMapper\Resolver;

use DataMapper\Interface\ElementArrayInterface;
use DataMapper\Interface\ElementDataInterface;
use DataMapper\Interface\ElementObjectInterface;
use Exception;
use InvalidArgumentException;

final readonly class ElementValueResolver
{
    public function __construct(
        private ElementDataInterface $elementData,
    ) {
        if ($elementData instanceof ElementObjectInterface) {
            throw new InvalidArgumentException('ObjectElementInterface not supported');
        }

        if ($elementData instanceof ElementArrayInterface) {
            throw new InvalidArgumentException('DataObject not supported');
        }
    }

    /**
     * @throws Exception
     */
    public function resolve(): mixed
    {
        return $this->elementData->getValue();
    }
}
