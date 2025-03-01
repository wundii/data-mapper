<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Interface;

/**
 * @template T of object
 */
interface SourceDataInterface
{
    public function __construct(DataConfigInterface $dataConfig, string $source, string|object $object);

    /**
     * @return T|T[]
     */
    public function resolve(): object|array;
}
