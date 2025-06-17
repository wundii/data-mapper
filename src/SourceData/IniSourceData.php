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
final class IniSourceData extends AbstractSourceData
{
    public const SOURCE_TYPE = SourceTypeEnum::INI;

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

        $iniArray = parse_ini_string($this->source, true);
        if (! is_array($iniArray)) {
            throw DataMapperException::InvalidArgument(sprintf('Invalid %s decode return', $sourceTypeEnum->value));
        }

        $arraySourceData = new ArraySourceData(
            $this->dataConfig,
            $iniArray,
            $this->object,
            $this->rootElementTree,
            $this->forceInstance,
        );

        return $arraySourceData->resolve(self::SOURCE_TYPE);
    }
}
