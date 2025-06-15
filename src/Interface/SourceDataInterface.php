<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Interface;

/**
 * @template T of object
 */
interface SourceDataInterface
{
    /**
     * @param string|array<int|string, mixed>|object $source
     * @param class-string<T>|T $object
     * @param string[] $rootElementTree
     */
    public function __construct(
        DataConfigInterface $dataConfig,
        string|array|object $source,
        string|object $object,
        array $rootElementTree = [],
        bool $forceInstance = false,
    );

    /**
     * @return T|T[]
     */
    public function resolve(): object|array;
}
