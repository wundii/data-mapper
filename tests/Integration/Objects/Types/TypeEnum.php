<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\Types;

enum TypeEnum: string
{
    case BERLIN = 'Berlin';
    case TOKYO = 'Tokyo';
    case LONDON = 'London';
}
