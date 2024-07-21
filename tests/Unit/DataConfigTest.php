<?php

declare(strict_types=1);

namespace DataMapper\Tests\Unit;

use DataMapper\DataConfig;
use DataMapper\Enum\AccessibleEnum;
use DataMapper\Enum\ApproachEnum;
use DataMapper\Tests\MockClasses\MapperClassConstructor;
use DataMapper\Tests\MockClasses\MapperClassInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DataConfigTest extends TestCase
{
    public function testDataConfigDefault(): void
    {
        $config = new DataConfig();

        $this->assertInstanceOf(DataConfig::class, $config);
        $this->assertEquals(ApproachEnum::CONSTRUCTOR, $config->getApproach());
        $this->assertEquals(AccessibleEnum::PUBLIC, $config->getAccessible());
        $this->assertIsArray($config->getClassMap());
        $this->assertCount(0, $config->getClassMap());
    }

    public function testDataConfigClassExceptionNoStringKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The class map must contain only strings');

        new DataConfig(classMap: [1 => MapperClassConstructor::class]);
    }

    public function testDataConfigClassExceptionNoStringValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The class map must contain only strings');

        new DataConfig(classMap: [MapperClassConstructor::class => 1]);
    }

    public function testDataConfigClassExceptionKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The key class does not exist');

        new DataConfig(classMap: ['failKey' => MapperClassConstructor::class]);
    }

    public function testDataConfigClassExceptionValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value class does not exist');

        new DataConfig(classMap: [MapperClassInterface::class => 'failValue']);
    }

    public function testDataConfigCustom(): void
    {
        $config = new DataConfig(
            ApproachEnum::PROPERTY,
            AccessibleEnum::PRIVATE,
            [MapperClassInterface::class => MapperClassConstructor::class]
        );

        $this->assertInstanceOf(DataConfig::class, $config);
        $this->assertEquals(ApproachEnum::PROPERTY, $config->getApproach());
        $this->assertEquals(AccessibleEnum::PRIVATE, $config->getAccessible());
        $this->assertIsArray($config->getClassMap());
        $this->assertCount(1, $config->getClassMap());
        $this->assertArrayHasKey(MapperClassInterface::class, $config->getClassMap());
        $this->assertEquals(MapperClassConstructor::class, $config->getClassMap()[MapperClassInterface::class]);


        $config = new DataConfig(
            ApproachEnum::PROPERTY,
            AccessibleEnum::PRIVATE,
            [MapperClassConstructor::class => MapperClassConstructor::class]
        );
        $this->assertInstanceOf(DataConfig::class, $config);
        $this->assertArrayHasKey(MapperClassConstructor::class, $config->getClassMap());
    }
}
