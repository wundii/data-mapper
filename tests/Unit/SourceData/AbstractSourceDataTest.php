<?php

declare(strict_types=1);

namespace Unit\SourceData;

use Exception;
use MockClasses\AbstractSourceDataTest as AbstractSourceData;
use MockClasses\RootProperties;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\Enum\ApproachEnum;

class AbstractSourceDataTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testReflectionObjectString(): void
    {
        $this->expectNotToPerformAssertions();

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $abstractSourceData = new AbstractSourceData(
            $dataConfig,
            'source',
            'object'
        );

        $abstractSourceData->resolveObjectDto('MockClasses\RootConstructor');
        $abstractSourceData->resolveObjectDto('MockClasses\RootSetters');
    }

    /**
     * @throws Exception
     */
    public function testReflectionObjectStringDuplicate(): void
    {
        $this->expectNotToPerformAssertions();

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $abstractSourceData = new AbstractSourceData(
            $dataConfig,
            'source',
            'object'
        );

        $abstractSourceData->resolveObjectDto('MockClasses\RootConstructor');
        $abstractSourceData->resolveObjectDto('MockClasses\ItemConstructor');
    }

    /**
     * @throws Exception
     */
    public function testReflectionObjectObject(): void
    {
        $this->expectNotToPerformAssertions();

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $abstractSourceData = new AbstractSourceData(
            $dataConfig,
            'source',
            'object'
        );

        $abstractSourceData->resolveObjectDto(new RootProperties());
    }
}
