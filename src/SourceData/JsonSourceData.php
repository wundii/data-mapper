<?php

declare(strict_types=1);

namespace Wundii\DataMapper\SourceData;

use ReflectionException;
use Wundii\DataMapper\Enum\SourceTypeEnum;
use Wundii\DataMapper\Exception\DataMapperException;

/**
 * @template T of object
 * @extends AbstractSourceData<T>
 */
final class JsonSourceData extends AbstractSourceData
{
    public const SOURCE_TYPE = SourceTypeEnum::JSON;

    /**
     * @return T|T[]
     * @throws DataMapperException|ReflectionException
     */
    public function resolve(): object|array
    {
        $sourceTypeEnum = self::SOURCE_TYPE;

        if (! is_string($this->source)) {
            throw DataMapperException::Error(sprintf('The %s source is not a string', $sourceTypeEnum->value));
        }

        $jsonArray = json_decode($this->source, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw DataMapperException::InvalidArgument(sprintf('Invalid %s string', $sourceTypeEnum->value));
        }

        if (! is_array($jsonArray)) {
            throw DataMapperException::InvalidArgument(sprintf('Invalid %s decode return', $sourceTypeEnum->value));
        }

        $arraySourceData = new ArraySourceData(
            $this->dataConfig,
            $jsonArray,
            $this->objectOrClass,
            $this->rootElementTree,
            $this->forceInstance,
        );

        return $arraySourceData->resolve(self::SOURCE_TYPE);
    }
}
