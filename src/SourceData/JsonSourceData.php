<?php

declare(strict_types=1);

namespace Wundii\DataMapper\SourceData;

use ReflectionException;
use Wundii\DataMapper\Enum\SourceTypeEnum;
use Wundii\DataMapper\Exception\DataMapperException;

if (PHP_VERSION_ID < 80300) {
    function json_validate(string $string): bool
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}

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
        $sourceTypeEnum = SourceTypeEnum::JSON;

        if (! is_string($this->source)) {
            throw DataMapperException::Error(sprintf('The %s source is not a string', $sourceTypeEnum->value));
        }

        if (! json_validate($this->source)) {
            throw DataMapperException::InvalidArgument(sprintf('Invalid %s string', $sourceTypeEnum->value));
        }

        $jsonArray = json_decode($this->source, true);
        if (! is_array($jsonArray)) {
            throw DataMapperException::InvalidArgument(sprintf('Invalid %s decode return', $sourceTypeEnum->value));
        }

        $arraySourceData = new ArraySourceData(
            $this->dataConfig,
            $jsonArray,
            $this->object,
            $this->rootElementTree,
            $this->forceInstance,
        );

        return $arraySourceData->resolve(self::SOURCE_TYPE);
    }
}
