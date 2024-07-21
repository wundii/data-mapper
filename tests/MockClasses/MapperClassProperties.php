<?php

declare(strict_types=1);

namespace DataMapper\Tests\MockClasses;

final class MapperClassProperties implements MapperClassInterface
{
    public string $name;

    public ?int $id = null;
}
