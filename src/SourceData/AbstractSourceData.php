<?php

declare(strict_types=1);

namespace DataMapper\SourceData;

use DataMapper\Interface\SourceDataInterface;
use InvalidArgumentException;

abstract class AbstractSourceData implements SourceDataInterface
{
    public function createInstanceFromString(string $objectName, mixed ...$parameters): object
    {
        if (! class_exists($objectName)) {
            throw new InvalidArgumentException(sprintf('Class %s not found', $objectName));
        }

        return new $objectName(...$parameters);
    }
}
