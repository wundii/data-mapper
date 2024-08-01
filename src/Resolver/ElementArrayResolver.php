<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use ReflectionException;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\Interface\ElementArrayInterface;
use Wundii\DataMapper\Interface\ElementDataInterface;
use Wundii\DataMapper\Interface\ElementObjectInterface;
use Wundii\DataMapper\Interface\ElementValueInterface;

final readonly class ElementArrayResolver
{
    /**
     * @throws DataMapperException|ReflectionException
     */
    public function matchValue(
        DataConfigInterface $dataConfig,
        ElementDataInterface $elementData,
    ): mixed {
        $elementObjectResolver = new ElementObjectResolver();
        $elementValueResolver = new ElementValueResolver();

        return match (true) {
            $elementData instanceof ElementArrayInterface => $this->resolve($dataConfig, $elementData),
            $elementData instanceof ElementObjectInterface => $elementObjectResolver->resolve($dataConfig, $elementData),
            $elementData instanceof ElementValueInterface => $elementValueResolver->resolve($elementData),
            default => throw DataMapperException::Error('ElementInterface not implemented: ' . $elementData::class),
        };
    }

    /**
     * @return mixed[]
     * @throws DataMapperException|ReflectionException
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
