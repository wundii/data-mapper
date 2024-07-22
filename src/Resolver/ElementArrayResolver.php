<?php

declare(strict_types=1);

namespace DataMapper\Resolver;

use DataMapper\DataConfig;
use DataMapper\Elements\DataArray;
use DataMapper\Elements\DataObject;
use DataMapper\Interface\ElementArrayInterface;
use Exception;

final readonly class ElementArrayResolver
{
    public function __construct(
        private DataConfig            $dataConfig,
        private ElementArrayInterface $arrayElement,
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
                DataObject::class => (new ElementObjectResolver($this->dataConfig, $dataElement))->resolve(),
                DataArray::class => (new self($this->dataConfig, $dataElement))->resolve(),
                default => (new ElementValueResolver($dataElement))->resolve(),
            };
        }

        return $return;
    }
}
