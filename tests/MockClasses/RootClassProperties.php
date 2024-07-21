<?php

declare(strict_types=1);

namespace DataMapper\Tests\MockClasses;

final class RootClassProperties implements RootClassInterface
{
    public string $name;

    public ?int $id = null;
}
