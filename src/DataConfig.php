<?php

declare(strict_types=1);

namespace Wundii\DataMapper;

use InvalidArgumentException;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Interface\DataConfigInterface;

final readonly class DataConfig implements DataConfigInterface
{
    /**
     * @param string[] $classMap
     */
    public function __construct(
        private ApproachEnum $approachEnum = ApproachEnum::CONSTRUCTOR,
        private AccessibleEnum $accessibleEnum = AccessibleEnum::PUBLIC,
        private array $classMap = [],
    ) {
        foreach ($classMap as $key => $value) {
            if (! is_string($key) || ! is_string($value)) {
                throw new InvalidArgumentException('The class map must contain only strings');
            }

            if (! interface_exists($key) && ! class_exists($key)) {
                throw new InvalidArgumentException('The key class does not exist');
            }

            if (! class_exists($value)) {
                throw new InvalidArgumentException('The value class does not exist');
            }
        }
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
}
