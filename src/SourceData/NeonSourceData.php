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
final class NeonSourceData extends AbstractSourceData
{
    public const SOURCE_TYPE = SourceTypeEnum::NEON;

    /**
     * @return T|T[]
     * @throws DataMapperException|ReflectionException
     */
    public function resolve(): object|array
    {
        $sourceTypeEnum = self::SOURCE_TYPE;

        if (! class_exists(\Nette\Neon\Neon::class)) {
            throw DataMapperException::Error('You need the Package Repository nette/neon');
        }

        if (! is_string($this->source)) {
            throw DataMapperException::Error(sprintf('The %s source is not a string', $sourceTypeEnum->value));
        }

        $neonArray = \Nette\Neon\Neon::decode($this->source);
        if (! is_array($neonArray)) {
            throw DataMapperException::InvalidArgument(sprintf('Invalid %s decode return', $sourceTypeEnum->value));
        }

        $arraySourceData = new ArraySourceData(
            $this->dataConfig,
            $neonArray,
            $this->objectOrClass,
            $this->rootElementTree,
            $this->forceInstance,
        );

        return $arraySourceData->resolve(self::SOURCE_TYPE);
    }
}
