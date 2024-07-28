<?php

declare(strict_types=1);

namespace MockClasses;

use Wundii\DataMapper\Reflection\ObjectReflection;
use Wundii\DataMapper\SourceData\AbstractSourceData;

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
