<?php

declare(strict_types=1);

namespace DataMapper\Tests\MockClasses;

enum TestStringEnum: string
{
    case ONE = 'one';
    case TWO = 'two';
    case THREE = 'three';
}
