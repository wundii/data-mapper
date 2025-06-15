<?php

declare(strict_types=1);

namespace Wundii\DataMapper;

use Wundii\DataMapper\Enum\SourceTypeEnum;
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\SourceData\ArraySourceData;
use Wundii\DataMapper\SourceData\JsonSourceData;
use Wundii\DataMapper\SourceData\ObjectSourceData;
use Wundii\DataMapper\SourceData\XmlSourceData;

/**
 * @template T of object
 */
class DataMapper
{
    public function __construct(
        private ?DataConfigInterface $dataConfig = null,
    ) {
    }

    public function setDataConfig(DataConfigInterface $dataConfig): void
    {
        $this->dataConfig = $dataConfig;
    }

    /**
     * @param array<mixed>|object $source
     * @param class-string<T>|T $object
     * @param string[] $rootElementTree // only possible in conjunction with the method toArray
     * @param bool $forceInstance // create a new instance, if no data can be found for the object
     * @return ($object is class-string ? T : T[])
     */
    public function object(
        array|object $source,
        string|object $object,
        array $rootElementTree = [],
        bool $forceInstance = false,
    ): object|array {
        return $this->map(SourceTypeEnum::OBJECT, $source, $object, $rootElementTree, $forceInstance);
    }

    /**
     * @param mixed[] $source
     * @param class-string<T>|T $object
     * @param string[] $rootElementTree
     * @param bool $forceInstance // create a new instance, if no data can be found for the object
     * @return ($object is class-string ? T : T[])
     */
    public function array(
        array $source,
        string|object $object,
        array $rootElementTree = [],
        bool $forceInstance = false,
    ): object|array {
        return $this->map(SourceTypeEnum::ARRAY, $source, $object, $rootElementTree, $forceInstance);
    }

    /**
     * @param class-string<T>|T $object
     * @param string[] $rootElementTree
     * @param bool $forceInstance // create a new instance, if no data can be found for the object
     * @return ($object is class-string ? T : T[])
     */
    public function json(
        string $source,
        string|object $object,
        array $rootElementTree = [],
        bool $forceInstance = false,
    ): object|array {
        return $this->map(SourceTypeEnum::JSON, $source, $object, $rootElementTree, $forceInstance);
    }

    /**
     * @param class-string<T>|T $object
     * @param string[] $rootElementTree
     * @param bool $forceInstance // create a new instance, if no data can be found for the object
     * @return ($object is class-string ? T : T[])
     */
    public function xml(
        string $source,
        string|object $object,
        array $rootElementTree = [],
        bool $forceInstance = false,
    ): object|array {
        return $this->map(SourceTypeEnum::XML, $source, $object, $rootElementTree, $forceInstance);
    }

    /**
     * @param string|array<mixed>|object $source
     * @param class-string<T>|T $object
     * @param string[] $rootElementTree
     * @param bool $forceInstance // create a new instance, if no data can be found for the object
     * @return ($object is class-string ? T : T[])
     */
    private function map(
        SourceTypeEnum $sourceTypeEnum,
        string|array|object $source,
        string|object $object,
        array $rootElementTree = [],
        bool $forceInstance = false,
    ): object|array {
        if (! $this->dataConfig instanceof DataConfigInterface) {
            $this->dataConfig = new DataConfig();
        }

        $sourceData = match ($sourceTypeEnum) {
            SourceTypeEnum::ARRAY => new ArraySourceData($this->dataConfig, $source, $object, $rootElementTree, $forceInstance),
            SourceTypeEnum::JSON => new JsonSourceData($this->dataConfig, $source, $object, $rootElementTree, $forceInstance),
            SourceTypeEnum::OBJECT => new ObjectSourceData($this->dataConfig, $source, $object, $rootElementTree, $forceInstance),
            SourceTypeEnum::XML => new XmlSourceData($this->dataConfig, $source, $object, $rootElementTree, $forceInstance),
        };

        return $sourceData->resolve();
    }
}
