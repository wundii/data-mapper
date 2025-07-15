<?php

declare(strict_types=1);

namespace Wundii\DataMapper;

use Wundii\DataMapper\Dto\CsvDto;
use Wundii\DataMapper\Enum\SourceTypeEnum;
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\SourceData\ArraySourceData;
use Wundii\DataMapper\SourceData\CsvSourceData;
use Wundii\DataMapper\SourceData\JsonSourceData;
use Wundii\DataMapper\SourceData\NeonSourceData;
use Wundii\DataMapper\SourceData\ObjectSourceData;
use Wundii\DataMapper\SourceData\XmlSourceData;
use Wundii\DataMapper\SourceData\YamlSourceData;

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
    public function csv(
        string $source,
        string|object $object,
        array $rootElementTree = [],
        bool $forceInstance = false,
        string $separator = CsvDto::DEFAULT_SEPARATOR,
        string $enclosure = CsvDto::DEFAULT_ENCLOSURE,
        string $escape = CsvDto::DEFAULT_ESCAPE,
        int $headerLine = CsvDto::DEFAULT_HEADER_LINE,
        int $firstLine = CsvDto::DEFAULT_FIRST_LINE,
    ): object|array {
        $csvDto = new CsvDto(
            $source,
            $separator,
            $enclosure,
            $escape,
            $headerLine,
            $firstLine,
        );

        return $this->map(SourceTypeEnum::CSV, $csvDto, $object, $rootElementTree, $forceInstance);
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
    public function neon(
        string $source,
        string|object $object,
        array $rootElementTree = [],
        bool $forceInstance = false,
    ): object|array {
        return $this->map(SourceTypeEnum::NEON, $source, $object, $rootElementTree, $forceInstance);
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
     * @param class-string<T>|T $object
     * @param string[] $rootElementTree
     * @param bool $forceInstance // create a new instance, if no data can be found for the object
     * @return ($object is class-string ? T : T[])
     */
    public function yaml(
        string $source,
        string|object $object,
        array $rootElementTree = [],
        bool $forceInstance = false,
    ): object|array {
        return $this->map(SourceTypeEnum::YAML, $source, $object, $rootElementTree, $forceInstance);
    }

    /**
     * @param string|array<mixed>|object $source
     * @param class-string<T>|T $object
     * @param string[] $rootElementTree
     * @param bool $forceInstance // create a new instance, if no data can be found for the object
     * @return ($object is class-string ? T : T[])
     */
    public function map(
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
            SourceTypeEnum::CSV => new CsvSourceData($this->dataConfig, $source, $object, $rootElementTree, $forceInstance),
            SourceTypeEnum::JSON => new JsonSourceData($this->dataConfig, $source, $object, $rootElementTree, $forceInstance),
            SourceTypeEnum::NEON => new NeonSourceData($this->dataConfig, $source, $object, $rootElementTree, $forceInstance),
            SourceTypeEnum::OBJECT => new ObjectSourceData($this->dataConfig, $source, $object, $rootElementTree, $forceInstance),
            SourceTypeEnum::XML => new XmlSourceData($this->dataConfig, $source, $object, $rootElementTree, $forceInstance),
            SourceTypeEnum::YAML => new YamlSourceData($this->dataConfig, $source, $object, $rootElementTree, $forceInstance),
        };

        return $sourceData->resolve();
    }
}
