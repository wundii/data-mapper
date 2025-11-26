<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

use DateInterval;
use DateTimeInterface;
use LogicException;
use Psr\Cache\CacheItemInterface;

final readonly class CacheItemDto implements CacheItemInterface
{
    public function __construct(
        private string $fileHash,
        private ?ReflectionObjectDto $reflectionObjectDto = null,
    ) {
    }

    public function getKey(): string
    {
        return $this->fileHash;
    }

    public function get(): ?ReflectionObjectDto
    {
        return $this->reflectionObjectDto;
    }

    public function isHit(): bool
    {
        return true;
    }

    public function set(mixed $value): static
    {
        throw new LogicException('Setting value is not supported in this implementation.');
    }

    public function expiresAt(?DateTimeInterface $expiration): static
    {
        return $this;
    }

    public function expiresAfter(DateInterval|int|null $time): static
    {
        return $this;
    }
}
