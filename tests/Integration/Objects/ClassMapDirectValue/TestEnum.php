<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\ClassMapDirectValue;

enum TestEnum: string
{
    case BERLIN = 'Berlin';
    case TOKYO = 'Tokyo';
    case LONDON = 'London';
}
