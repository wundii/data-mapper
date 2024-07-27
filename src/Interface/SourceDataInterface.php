<?php

declare(strict_types=1);

namespace DataMapper\Interface;

interface SourceDataInterface
{
    public function __construct(DataConfigInterface $dataConfig, string $source, string|object $object);

    public function resolve(): object;
}
