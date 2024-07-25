<?php

declare(strict_types=1);

namespace DataMapper;

use DataMapper\Enum\AccessibleEnum;
use DataMapper\Enum\ApproachEnum;
use InvalidArgumentException;

final readonly class DataConfig
{
    /**
     * @param (string|callable(string): string)[] $classMap
     */
    public function __construct(
        private ApproachEnum $approachEnum = ApproachEnum::CONSTRUCTOR,
        private AccessibleEnum $accessibleEnum = AccessibleEnum::PUBLIC,
        private array $classMap = [],
    ) {
        foreach ($classMap as $key => $value) {
            /** @phpstan-ignore-next-line */
            if (! is_string($key) || (! is_string($value) && ! is_callable($value))) {
                throw new InvalidArgumentException('The class map must contain only strings or callables');
            }

            if (! interface_exists($key) && ! class_exists($key)) {
                throw new InvalidArgumentException('The key class does not exist: ' . $key);
            }

            if (is_string($value) && ! class_exists($value)) {
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
     * @return (string|callable(string): string)[]
     */
    public function getClassMap(): array
    {
        return $this->classMap;
    }

    public function mapClassName(string $objectName): string
    {
        $value = $this->classMap[$objectName] ?? $objectName;

        if (is_callable($value)) {
            /**
             * @todo without function
             */
            $return = $value('dog');

            if (! is_string($return)) {
                throw new InvalidArgumentException('The class map callable must return a string');
            }

            if (! interface_exists($return) && ! class_exists($return)) {
                throw new InvalidArgumentException('The key class does not exist: ' . $return);
            }

            return $return;
        }

        return $value;
    }
}
