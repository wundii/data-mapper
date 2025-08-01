<?php

declare(strict_types=1);

namespace Wundii\DataMapper;

use Exception;
use Psr\Cache\CacheItemPoolInterface;
use ReflectionClass;
use Wundii\DataMapper\Dto\CacheItemDto;
use Wundii\DataMapper\Dto\ReflectionObjectDto;

class DataObjectCache
{
    public const HASH_ALGORITHM = 'sha256';

    public function __construct(
        private ?CacheItemPoolInterface $cacheItemPool,
    ) {
    }

    /**
     * @var ReflectionObjectDto[]
     */
    private static array $cache = [];

    public function getItem(object|string $objectOrClass): ?ReflectionObjectDto
    {
        $stringClass = is_object($objectOrClass) ? get_class($objectOrClass) : $objectOrClass;

        if (! $this->hasItem($stringClass)) {
            return null;
        }

        return self::$cache[$stringClass];
    }

    public function hasItem(string $stringClass): bool
    {
        $hasItem = array_key_exists($stringClass, self::$cache);
        if ($hasItem) {
            return true;
        }

        if ($this->cacheItemPool instanceof CacheItemPoolInterface) {
            try {
                $reflectionClass = new ReflectionClass($stringClass);
                $fileName = $reflectionClass->getFileName();
                if ($fileName === false) {
                    return false;
                }

                $fileHash = hash_file(self::HASH_ALGORITHM, $fileName);

                $cacheItemDto = $this->cacheItemPool->getItem($fileHash);

                // dump($this->cacheItemPool);
                // dump($cacheItemDto);
                if ($cacheItemDto->get() instanceof ReflectionObjectDto) {
                    self::$cache[$stringClass] = $cacheItemDto->get();
                    return true;
                }

                return false;
            } catch (Exception) {
                return false;
            }
        }

        return false;
    }

    public function save(ReflectionObjectDto $reflectionObjectDto): ReflectionObjectDto
    {
        self::$cache[$reflectionObjectDto->getClassString()] = $reflectionObjectDto;

        $this->saveCacheItem($reflectionObjectDto);

        return $reflectionObjectDto;
    }

    public function saveCacheItem(ReflectionObjectDto $reflectionObjectDto): void
    {
        if (!$this->cacheItemPool instanceof CacheItemPoolInterface) {
            return;
        }

        $cacheItemDto = new CacheItemDto(
            $reflectionObjectDto->getFileHash(),
            $reflectionObjectDto,
        );

        $this->cacheItemPool->save($cacheItemDto);
    }
}
