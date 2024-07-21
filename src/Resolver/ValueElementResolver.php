<?php

declare(strict_types=1);

namespace DataMapper\Resolver;

use DataMapper\Interface\ArrayElementInterface;
use DataMapper\Interface\DataElementInterface;
use DataMapper\Interface\ObjectElementInterface;
use Exception;
use InvalidArgumentException;

final readonly class ValueElementResolver
{
    public function __construct(
        private DataElementInterface $dataElement,
    ) {
        if ($dataElement instanceof ObjectElementInterface) {
            throw new InvalidArgumentException('ObjectElementInterface not supported');
        }

        if ($dataElement instanceof ArrayElementInterface) {
            throw new InvalidArgumentException('DataObject not supported');
        }
    }

    /**
     * @throws Exception
     */
    public function resolve(): mixed
    {
        return $this->dataElement->getValue();
    }
}

// $properties = array_map(
//     static fn (DataElementInterface $dataElement): mixed => $dataElement->getValue(),
//     $this->value,
// );
//
// $objectName = $this->config->mapClassName($this->objectName);
//
// $instance = match($this->config->getApproach()) {
//     ApproachEnum::CONSTRUCTOR => new $objectName(...$properties),
//     default => new $objectName,
// };
//
// return match ($this->config->getApproach()) {
//     ApproachEnum::PROPERTY => $this->setProperties($instance, $properties),
//     ApproachEnum::SETTER => $this->setProperties($instance, $properties),
//     default => $instance,
// };
