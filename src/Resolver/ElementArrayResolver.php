<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use Wundii\DataMapper\Elements\DataArray;
use Wundii\DataMapper\Elements\DataObject;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\Interface\ElementArrayInterface;
use Wundii\DataMapper\Interface\ElementDataInterface;

final readonly class ElementArrayResolver
{
    /**
     * @throws DataMapperException
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
     * @return mixed[]
     * @throws DataMapperException
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
