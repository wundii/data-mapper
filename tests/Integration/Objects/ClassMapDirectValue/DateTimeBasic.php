<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\ClassMapDirectValue;

use DataMapper\Tests\MockClasses\RootInterface;
use DateTimeInterface;

final class DateTimeBasic implements RootInterface
{
    public DateTimeInterface $created;
}
