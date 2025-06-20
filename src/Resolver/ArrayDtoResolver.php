<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use ReflectionException;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\ArrayDtoInterface;
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\Interface\ObjectDtoInterface;
use Wundii\DataMapper\Interface\TypeDtoInterface;
use Wundii\DataMapper\Interface\ValueDtoInterface;

final readonly class ArrayDtoResolver
{
    /**
     * @throws DataMapperException|ReflectionException
     */
    public function matchValue(
        DataConfigInterface $dataConfig,
        TypeDtoInterface $typeDto,
    ): mixed {
        $objectDtoResolver = new ObjectDtoResolver();
        $valueDtoResolver = new ValueDtoResolver();

        return match (true) {
            $typeDto instanceof ArrayDtoInterface => $this->resolve($dataConfig, $typeDto),
            $typeDto instanceof ObjectDtoInterface => $objectDtoResolver->resolve($dataConfig, $typeDto),
            $typeDto instanceof ValueDtoInterface => $valueDtoResolver->resolve($typeDto),
            default => throw DataMapperException::Error('TypeDtoInterface not implemented: ' . $typeDto::class),
        };
    }

    /**
     * @return mixed[]
     * @throws DataMapperException|ReflectionException
     */
    public function resolve(
        DataConfigInterface $dataConfig,
        ArrayDtoInterface $arrayDto
    ): array {
        $return = [];

        foreach ($arrayDto->getValue() as $key => $typeDto) {
            $data = $this->matchValue($dataConfig, $typeDto);

            if ($typeDto instanceof ObjectDtoInterface && $data === null) {
                continue;
            }

            $return[$key] = $data;
        }

        return $return;
    }
}
