<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\ClassMapDirectValue;

use DataMapper\Tests\MockClasses\RootInterface;
use DateTimeInterface as PhpDateTimeInterface;

final class DateTimeAlias implements RootInterface
{
    public PhpDateTimeInterface $created;
}
