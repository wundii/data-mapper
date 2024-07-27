<?php

declare(strict_types=1);

namespace DataMapper\Tests\MockClasses;

use DataMapper\Reflection\ObjectReflection;
use DataMapper\SourceData\AbstractSourceData;

class AbstractSourceDataTest extends AbstractSourceData
{
    /**
     * @return ObjectReflection[]
     */
    public function getReflectionObjects(): array
    {
        return self::$objectReflections;
    }

    public function resolve(): object
    {
        return new \stdClass();
    }
}
