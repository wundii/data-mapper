<?php

declare(strict_types=1);

namespace Wundii\DataMapper;

use Wundii\DataMapper\Enum\SourceTypeEnum;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\SourceData\JsonSourceData;
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
     * @param mixed[] $source
     * @param class-string<T>|T $object
     * @param string[] $rootElementTree
     * @return ($object is class-string ? T : T[])
     */
    public function array(
        array $source,
        string|object $object,
        array $rootElementTree = [],
    ): object|array {
        $json = json_encode($source);

        if ($json === false) {
            throw DataMapperException::InvalidArgument('Could not encode the array to JSON');
        }

        return $this->map(SourceTypeEnum::JSON, $json, $object, $rootElementTree);
    }

    /**
     * @param class-string<T>|T $object
     * @param string[] $rootElementTree
     * @return ($object is class-string ? T : T[])
     */
    public function json(
        string $source,
        string|object $object,
        array $rootElementTree = [],
    ): object|array {
        return $this->map(SourceTypeEnum::JSON, $source, $object, $rootElementTree);
    }

    /**
     * @param class-string<T>|T $object
     * @param string[] $rootElementTree
     * @return ($object is class-string ? T : T[])
     */
    public function xml(
        string $source,
        string|object $object,
        array $rootElementTree = [],
    ): object|array {
        return $this->map(SourceTypeEnum::XML, $source, $object, $rootElementTree);
    }

    /**
     * @param class-string<T>|T $object
     * @param string[] $rootElementTree
     * @return ($object is class-string ? T : T[])
     */
    private function map(
        SourceTypeEnum $sourceTypeEnum,
        string $source,
        string|object $object,
        array $rootElementTree = [],
    ): object|array {
        if (! $this->dataConfig instanceof DataConfigInterface) {
            $this->dataConfig = new DataConfig();
        }

        $sourceData = match ($sourceTypeEnum) {
            SourceTypeEnum::JSON => new JsonSourceData($this->dataConfig, $source, $object, $rootElementTree),
            SourceTypeEnum::XML => new XmlSourceData($this->dataConfig, $source, $object, $rootElementTree),
        };

        return $sourceData->resolve();
    }
}
