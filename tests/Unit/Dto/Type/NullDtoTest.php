<?php

declare(strict_types=1);

namespace Unit\Dto\Type;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Dto\Type\NullDto;

class NullDtoTest extends TestCase
{
    public function testInstanceToString(): void
    {
        $nullDto = new NullDto();
        $this->assertSame('null', (string) $nullDto);
    }
}
