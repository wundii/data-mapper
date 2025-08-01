<?php

declare(strict_types=1);

namespace Wundii\DataMapper;

use Psr\Cache\CacheItemPoolInterface;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\DataConfigInterface;

final readonly class DataConfig implements DataConfigInterface
{
    private DataObjectCache $dataObjectCache;

    /**
     * @param string[] $classMap
     */
    public function __construct(
        private ApproachEnum $approachEnum = ApproachEnum::SETTER,
        private AccessibleEnum $accessibleEnum = AccessibleEnum::PUBLIC,
        private array $classMap = [],
        ?CacheItemPoolInterface $cacheItemPool = null,
    ) {
        foreach ($classMap as $key => $value) {
            /** for a better error output if the input does not have the correct data type */
            /** @phpstan-ignore function.alreadyNarrowedType */
            if (! is_string($key) || ! is_string($value)) {
                throw DataMapperException::InvalidArgument('The class map must contain only strings', [
                    'source' => (string) $key,
                    'target' => (string) $value,
                ]);
            }

            if (! interface_exists($key) && ! class_exists($key)) {
                throw DataMapperException::InvalidArgument('The key class does not exist', $key);
            }

            if (! class_exists($value)) {
                throw DataMapperException::InvalidArgument('The value class does not exist', $value);
            }
        }

        $this->dataObjectCache = new DataObjectCache($cacheItemPool);
    }

    public function getApproach(): ApproachEnum
    {
        return $this->approachEnum;
    }

    public function getAccessible(): AccessibleEnum
    {
        return $this->accessibleEnum;
    }

    /**
     * @return string[]
     */
    public function getClassMap(): array
    {
        return $this->classMap;
    }

    public function mapClassName(string $objectName): string
    {
        return $this->classMap[$objectName] ?? $objectName;
    }

    public function getDataObjectCache(): DataObjectCache
    {
        return $this->dataObjectCache;
    }
}
