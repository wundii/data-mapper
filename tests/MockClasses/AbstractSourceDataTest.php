<?php

declare(strict_types=1);

namespace MockClasses;

use Wundii\DataMapper\Dto\ReflectionObjectDto;
use Wundii\DataMapper\SourceData\AbstractSourceData;

class AbstractSourceDataTest extends AbstractSourceData
{
    /**
     * @return ReflectionObjectDto[]
     */
    public function getReflectionObjects(): array
    {
        return self::$objectPropertyDtos;
    }

    public function resolve(): object
    {
        return new \stdClass();
    }
}
