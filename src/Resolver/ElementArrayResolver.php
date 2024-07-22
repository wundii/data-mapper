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
        private DataConfig $dataConfig,
        private ElementArrayInterface $elementArray,
    ) {
    }

    /**
     * @throws Exception
     * @return mixed[]
     */
    public function resolve(): array
    {
        $return = [];

        foreach ($this->elementArray->getValue() as $dataKey => $elementData) {
            $return[$dataKey] = match (get_class($elementData)) {
                DataObject::class => (new ElementObjectResolver($this->dataConfig, $elementData))->resolve(),
                DataArray::class => (new self($this->dataConfig, $elementData))->resolve(),
                default => (new ElementValueResolver($elementData))->resolve(),
            };
        }

        return $return;
    }
}
