<?php

declare(strict_types=1);

namespace Wundii\DataMapper;

use Wundii\DataMapper\Enum\SourceTypeEnum;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\DataConfigInterface;

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
     * @return ($object is class-string ? T : object)
     */
    public function array(
        array $source,
        string|object $object,
        array $rootElementTree = [],
    ): object {
        $json = json_encode($source);

        if ($json === false) {
            throw DataMapperException::InvalidArgument('Could not encode the array to JSON');
        }

        return $this->map(SourceTypeEnum::JSON, $json, $object, $rootElementTree);
    }

    /**
     * @param class-string<T>|T $object
     * @param string[] $rootElementTree
     * @return ($object is class-string ? T : object)
     */
    public function json(
        string $source,
        string|object $object,
        array $rootElementTree = [],
    ): object {
        return $this->map(SourceTypeEnum::JSON, $source, $object, $rootElementTree);
    }

    /**
     * @param class-string<T>|T $object
     * @param string[] $rootElementTree
     * @return ($object is class-string ? T : object)
     */
    public function xml(
        string $source,
        string|object $object,
        array $rootElementTree = [],
    ): object {
        return $this->map(SourceTypeEnum::XML, $source, $object, $rootElementTree);
    }

    /**
     * @param class-string<T>|T $object
     * @param string[] $rootElementTree
     * @return ($object is class-string ? T : object)
     */
    private function map(
        SourceTypeEnum $sourceTypeEnum,
        string $source,
        string|object $object,
        array $rootElementTree = [],
    ): object {
        if (! $this->dataConfig instanceof DataConfigInterface) {
            $this->dataConfig = new DataConfig();
        }

        $sourceData = SourceTypeEnum::sourceDataInstance(
            $sourceTypeEnum,
            $this->dataConfig,
            $source,
            $object,
            $rootElementTree,
        );

        return $sourceData->resolve();
    }
}
