<?php

declare(strict_types=1);

namespace Wundii\DataMapper\CacheAdapter;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use RuntimeException;
use Wundii\DataMapper\Dto\CacheItemDto;

class FileCacheAdapter implements CacheItemPoolInterface
{
    private array $deferred = [];

    public function __construct(
        private string $path = '/tmp/datamapper',
    ) {
        if(!is_dir($this->path)) {
            if (!mkdir($this->path, 0777, true) && !is_dir($this->path)) {
                throw new RuntimeException("Unable to create cache directory '{$this->path}'");
            }
        }

        if (!is_readable($this->path)
            || !is_writable($this->path)) {
            throw new RuntimeException("Cache directory '{$this->path}' is not readable or writable");
        }
    }

    private function getFilePath(string $key): string
    {
        return $this->path . '/' . $key . '.cache';
    }

    public function getItem(string $key): CacheItemInterface
    {
        $array = iterator_to_array($this->getItems([$key]));

        return array_shift($array);
    }

    public function getItems(array $keys = []): iterable
    {
        foreach ($keys as $key) {
            if ($this->hasItem($key)) {
                $path = $this->getFilePath($key);
                $fileContent = file_get_contents($path);
                if ($fileContent === false) {
                    throw new RuntimeException("Unable to read file '{$path}'");
                }

                yield unserialize($fileContent);
            }

            yield new CacheItemDto($key);
        }
    }

    public function hasItem(string $key): bool
    {
        return file_exists($this->getFilePath($key));
    }

    public function clear(): bool
    {
        foreach (glob($this->path . '/*.cache') as $file) {
            unlink($file);
        }

        return true;
    }

    public function deleteItem(string $key): bool
    {
        $file = $this->getFilePath($key);
        return !file_exists($file) || unlink($file);
    }

    public function deleteItems(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->deleteItem($key);
        }
        return true;
    }

    public function save(CacheItemInterface $item): bool
    {
        return file_put_contents(
            $this->getFilePath($item->getKey()),
            serialize($item),
        ) !== false;
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->deferred[] = $item;
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
