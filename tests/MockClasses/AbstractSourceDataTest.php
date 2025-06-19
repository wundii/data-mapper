<?php

declare(strict_types=1);

namespace MockClasses;

use Wundii\DataMapper\Dto\ObjectDto;
use Wundii\DataMapper\SourceData\AbstractSourceData;

class AbstractSourceDataTest extends AbstractSourceData
{
    /**
     * @return ObjectDto[]
     */
    public function getReflectionObjects(): array
    {
        return self::$objectDtos;
    }

    public function resolve(): object
    {
        return new \stdClass();
    }
}
