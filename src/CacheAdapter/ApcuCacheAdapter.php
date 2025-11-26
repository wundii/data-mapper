<?php

declare(strict_types=1);

namespace Wundii\DataMapper\CacheAdapter;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use RuntimeException;
use Wundii\DataMapper\Dto\CacheItemDto;

class ApcuCacheAdapter implements CacheItemPoolInterface
{
    /**
     * @var CacheItemInterface[]
     */
    private array $deferred = [];

    public function __construct(
        private int $ttl = 3600,
    ) {
        if (! extension_loaded('apcu') || ! function_exists('apcu_enabled') || ! apcu_enabled()) {
            throw new RuntimeException('APCu extension is not enabled.');
        }
    }

    public function getItem(string $key): CacheItemInterface
    {
        $array = iterator_to_array($this->getItems([$key]));

        $object = array_shift($array);
        if (! $object instanceof CacheItemInterface) {
            throw new RuntimeException(sprintf('Cache item for key "%s" could not be retrieved.', $key));
        }

        return $object;
    }

    /**
     * @param string[] $keys
     * @return iterable<CacheItemInterface>
     * @throws RuntimeException
     */
    public function getItems(array $keys = []): iterable
    {
        foreach ($keys as $key) {
            if ($this->hasItem($key)) {
                $apcuValue = apcu_fetch($key);
                if (! is_string($apcuValue)) {
                    throw new RuntimeException(sprintf('Cache item for key "%s" could not be retrieved from APCu.', $key));
                }

                $object = unserialize($apcuValue);
                if (! $object instanceof CacheItemInterface) {
                    throw new RuntimeException(sprintf('Cache item for key "%s" is invalid.', $key));
                }

                yield $object;
            }

            yield new CacheItemDto($key);
        }
    }

    public function hasItem(string $key): bool
    {
        return apcu_exists($key);
    }

    public function clear(): bool
    {
        return false;
    }

    public function deleteItem(string $key): bool
    {
        return apcu_delete($key);
    }

    public function deleteItems(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->deleteItem($key);
        }

        return true;
    }

    public function save(CacheItemInterface $cacheItem): bool
    {
        return apcu_store($cacheItem->getKey(), serialize($cacheItem), $this->ttl);
    }

    public function saveDeferred(CacheItemInterface $cacheItem): bool
    {
        $this->deferred[] = $cacheItem;
        return true;
    }

    public function commit(): bool
    {
        foreach ($this->deferred as $item) {
            $this->save($item);
        }

        $this->deferred = [];

        return true;
    }
}
