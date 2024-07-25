<?php

declare(strict_types=1);

namespace DataMapper\Tests\MockClasses;

use DateTimeInterface;

final class RootClassMap implements RootInterface
{
    public string $name;

    public DateTimeInterface $created;
}
