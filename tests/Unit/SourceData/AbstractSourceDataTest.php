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

        $abstractSourceData->resolveObjectPropertyDto('MockClasses\RootConstructor');
        $abstractSourceData->resolveObjectPropertyDto('MockClasses\RootSetters');
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

        $abstractSourceData->resolveObjectPropertyDto('MockClasses\RootConstructor');
        $abstractSourceData->resolveObjectPropertyDto('MockClasses\ItemConstructor');
    }

    /**
     * @throws Exception
     */
    public function testReflectionObjectCheckStaticProperty(): void
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $abstractSourceData = new AbstractSourceData(
            $dataConfig,
            'source',
            'object'
        );

        $reflectionObjects = $abstractSourceData->getReflectionObjects();
        $expected = [
            'MockClasses\RootConstructor',
            'MockClasses\RootSetters',
            'MockClasses\ItemConstructor',
        ];

        $this->assertCount(3, $reflectionObjects);
        $this->assertSame($expected, array_keys($reflectionObjects));
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

        $abstractSourceData->resolveObjectPropertyDto(new RootProperties());
    }
}
