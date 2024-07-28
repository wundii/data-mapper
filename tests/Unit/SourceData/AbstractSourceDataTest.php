<?php

declare(strict_types=1);

namespace Unit\SourceData;

use DataMapper\DataConfig;
use Exception;
use MockClasses\AbstractSourceDataTest as AbstractSourceData;
use MockClasses\RootProperties;
use PHPUnit\Framework\TestCase;

class AbstractSourceDataTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testReflectionObjectString(): void
    {
        $this->expectNotToPerformAssertions();

        $dataConfig = new DataConfig();
        $abstractSourceData = new AbstractSourceData(
            $dataConfig,
            'source',
            'object'
        );

        $abstractSourceData->reflectionObject('MockClasses\RootConstructor');
        $abstractSourceData->reflectionObject('MockClasses\RootSetters');
    }

    /**
     * @throws Exception
     */
    public function testReflectionObjectStringDuplicate(): void
    {
        $this->expectNotToPerformAssertions();

        $dataConfig = new DataConfig();
        $abstractSourceData = new AbstractSourceData(
            $dataConfig,
            'source',
            'object'
        );

        $abstractSourceData->reflectionObject('MockClasses\RootConstructor');
        $abstractSourceData->reflectionObject('MockClasses\ItemConstructor');
    }

    /**
     * @throws Exception
     */
    public function testReflectionObjectCheckStaticProperty(): void
    {
        $dataConfig = new DataConfig();
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

        $dataConfig = new DataConfig();
        $abstractSourceData = new AbstractSourceData(
            $dataConfig,
            'source',
            'object'
        );

        $abstractSourceData->reflectionObject(new RootProperties());
    }
}
