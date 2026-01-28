<?php

declare(strict_types=1);

namespace Unit\Attribute;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Attribute\TargetData;

class TargetDataTest extends TestCase
{
    public function testGetAliasReturnsAlias(): void
    {
        $targetData = new TargetData('aliasValue');
        $this->assertSame('aliasValue', $targetData->getAlias());
    }
}
