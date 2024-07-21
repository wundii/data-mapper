<?php

declare(strict_types=1);

namespace DataMapper\Resolver;

use DataMapper\DataConfig;
use DataMapper\Elements\DataArray;
use DataMapper\Elements\DataObject;
use DataMapper\Interface\ArrayElementInterface;
use Exception;

final readonly class ArrayElementResolver
{
    public function __construct(
        private DataConfig $dataConfig,
        private ArrayElementInterface $arrayElement,
    ) {
    }

    /**
     * @throws Exception
     * @return mixed[]
     */
    public function resolve(): array
    {
        $return = [];

        foreach ($this->arrayElement->getValue() as $dataKey => $dataElement) {
            $return[$dataKey] = match (get_class($dataElement)) {
                DataObject::class => (new ObjectElementResolver($this->dataConfig, $dataElement))->resolve(),
                DataArray::class => (new self($this->dataConfig, $dataElement))->resolve(),
                default => (new ValueElementResolver($dataElement))->resolve(),
            };
        }

        return $return;
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
