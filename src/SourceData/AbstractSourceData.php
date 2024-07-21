<?php

declare(strict_types=1);

namespace DataMapper\SourceData;

use DataMapper\Interface\SourceDataInterface;
use InvalidArgumentException;

abstract class AbstractSourceData implements SourceDataInterface
{
    /**
     * @param array<mixed> $parameters
     */
    public function createInstanceFromString(string $objectName, array $parameters = []): object
    {
        if (! class_exists($objectName)) {
            throw new InvalidArgumentException(sprintf('Class %s not found', $objectName));
        }

        return new $objectName(...$parameters);
    }
}
