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
final class YamlSourceData extends AbstractSourceData
{
    public const SOURCE_TYPE = SourceTypeEnum::YAML;

    /**
     * @return T|T[]
     * @throws DataMapperException|ReflectionException
     */
    public function resolve(): object|array
    {
        $sourceTypeEnum = self::SOURCE_TYPE;

        if (! function_exists('yaml_parse')) {
            throw DataMapperException::Error('You must enable yaml extension from php');
        }

        if (! is_string($this->source)) {
            throw DataMapperException::Error(sprintf('The %s source is not a string', $sourceTypeEnum->value));
        }

        $yamlArray = yaml_parse($this->source);
        if (! is_array($yamlArray)) {
            throw DataMapperException::InvalidArgument(sprintf('Invalid %s decode return', $sourceTypeEnum->value));
        }

        $arraySourceData = new ArraySourceData(
            $this->dataConfig,
            $yamlArray,
            $this->objectOrClass,
            $this->rootElementTree,
            $this->forceInstance,
        );

        return $arraySourceData->resolve(self::SOURCE_TYPE);
    }
}
