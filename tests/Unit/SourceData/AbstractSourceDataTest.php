<?php

declare(strict_types=1);

namespace DataMapper\Tests\Unit\SourceData;

use DataMapper\DataConfig;
use DataMapper\Tests\MockClasses\AbstractSourceDataTest as AbstractSourceData;
use DataMapper\Tests\MockClasses\RootProperties;
use Exception;
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

        $abstractSourceData->reflectionObject('DataMapper\Tests\MockClasses\RootConstructor');
        $abstractSourceData->reflectionObject('DataMapper\Tests\MockClasses\RootSetters');
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

        $abstractSourceData->reflectionObject('DataMapper\Tests\MockClasses\RootConstructor');
        $abstractSourceData->reflectionObject('DataMapper\Tests\MockClasses\ItemConstructor');
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
            'DataMapper\Tests\MockClasses\RootConstructor',
            'DataMapper\Tests\MockClasses\RootSetters',
            'DataMapper\Tests\MockClasses\ItemConstructor',
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
