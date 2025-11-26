<?php

declare(strict_types=1);

namespace Wundii\DataMapper\CacheAdapter;

use InvalidArgumentException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Redis;
use RuntimeException;
use Wundii\DataMapper\Dto\CacheItemDto;

class RedisCacheAdapter implements CacheItemPoolInterface
{
    /**
     * @var CacheItemInterface[]
     */
    private array $deferred = [];

    public function __construct(
        private Redis $redis,
        private int $ttl = 3600,
    ) {
    }

    public function getItem(string $key): CacheItemInterface
    {
        $array = iterator_to_array($this->getItems([$key]));

        $object = array_shift($array);

        if (! $object instanceof CacheItemInterface) {
            throw new InvalidArgumentException(sprintf('Cache item for key "%s" could not be retrieved.', $key));
        }

        return $object;
    }

    /**
     * @param string[] $keys
     * @return iterable<CacheItemInterface>
     * @throws InvalidArgumentException
     */
    public function getItems(array $keys = []): iterable
    {
        foreach ($keys as $key) {
            if ($this->hasItem($key)) {
                $redisKey = $this->getRedisKey($key);
                $redisValue = $this->redis->get($redisKey);
                if (! is_string($redisValue)) {
                    throw new RuntimeException(sprintf('Cache item for key "%s" could not be retrieved from Redis.', $key));
                }

                $object = unserialize($redisValue);
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
        $redisKey = $this->getRedisKey($key);
        return (bool) $this->redis->exists($redisKey);
    }

    public function clear(): bool
    {
        return false;
    }

    public function deleteItem(string $key): bool
    {
        $redisKey = $this->getRedisKey($key);
        return (bool) $this->redis->del($redisKey);
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
        $redisKey = $this->getRedisKey($cacheItem->getKey());
        return $this->ttl > 0
            ? $this->redis->setex($redisKey, $this->ttl, serialize($cacheItem))
            : $this->redis->set($redisKey, serialize($cacheItem));
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

    private function getRedisKey(string $key): string
    {
        return 'datamapper:cache:' . $key;
    }
}
