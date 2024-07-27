<?php

declare(strict_types=1);

namespace DataMapper\Resolver;

use DataMapper\Elements\DataArray;
use DataMapper\Elements\DataObject;
use DataMapper\Interface\DataConfigInterface;
use DataMapper\Interface\ElementArrayInterface;
use DataMapper\Interface\ElementDataInterface;
use Exception;

final readonly class ElementArrayResolver
{
    /**
     * @throws Exception
     */
    public function matchValue(
        DataConfigInterface $dataConfig,
        ElementDataInterface $elementData,
    ): mixed {
        $elementObjectResolver = new ElementObjectResolver();
        $elementValueResolver = new ElementValueResolver();

        return match (get_class($elementData)) {
            DataArray::class => $this->resolve($dataConfig, $elementData),
            DataObject::class => $elementObjectResolver->resolve($dataConfig, $elementData),
            default => $elementValueResolver->resolve($elementData),
        };
    }

    /**
     * @throws Exception
     * @return mixed[]
     */
    public function resolve(
        DataConfigInterface $dataConfig,
        ElementArrayInterface $elementArray
    ): array {
        $return = [];

        foreach ($elementArray->getValue() as $dataKey => $elementData) {
            $return[$dataKey] = $this->matchValue($dataConfig, $elementData);
        }

        return $return;
    }
}
