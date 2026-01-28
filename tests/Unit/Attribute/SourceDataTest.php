<?php

declare(strict_types=1);

namespace Unit\Attribute;

use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Attribute\SourceData;

class SourceDataTest extends TestCase
{
    public function testGetAliasReturnsAlias(): void
    {
        $sourceData = new SourceData('aliasValue');
        $this->assertSame('aliasValue', $sourceData->getTarget());
    }
}
