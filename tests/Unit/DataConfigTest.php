<?php

declare(strict_types=1);

namespace DataMapper\Tests\Unit;

use DataMapper\DataConfig;
use DataMapper\Enum\AccessibleEnum;
use DataMapper\Enum\ApproachEnum;
use DataMapper\Tests\MockClasses\RootClassConstructor;
use DataMapper\Tests\MockClasses\RootClassInterface;
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

        new DataConfig(classMap: [
            1 => RootClassConstructor::class,
        ]);
    }

    public function testDataConfigClassExceptionNoStringValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The class map must contain only strings');

        new DataConfig(classMap: [
            RootClassConstructor::class => 1,
        ]);
    }

    public function testDataConfigClassExceptionKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The key class does not exist');

        new DataConfig(classMap: [
            'failKey' => RootClassConstructor::class,
        ]);
    }

    public function testDataConfigClassExceptionValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value class does not exist');

        new DataConfig(classMap: [
            RootClassInterface::class => 'failValue',
        ]);
    }

    public function testDataConfigCustom(): void
    {
        $config = new DataConfig(
            ApproachEnum::PROPERTY,
            AccessibleEnum::PRIVATE,
            [
                RootClassInterface::class => RootClassConstructor::class,
            ]
        );

        $this->assertInstanceOf(DataConfig::class, $config);
        $this->assertEquals(ApproachEnum::PROPERTY, $config->getApproach());
        $this->assertEquals(AccessibleEnum::PRIVATE, $config->getAccessible());
        $this->assertIsArray($config->getClassMap());
        $this->assertCount(1, $config->getClassMap());
        $this->assertArrayHasKey(RootClassInterface::class, $config->getClassMap());
        $this->assertEquals(RootClassConstructor::class, $config->getClassMap()[RootClassInterface::class]);
        $this->assertEquals(RootClassConstructor::class, $config->mapClassName(RootClassInterface::class));

        $config = new DataConfig(
            ApproachEnum::PROPERTY,
            AccessibleEnum::PRIVATE,
            [
                RootClassConstructor::class => RootClassConstructor::class,
            ]
        );
        $this->assertInstanceOf(DataConfig::class, $config);
        $this->assertArrayHasKey(RootClassConstructor::class, $config->getClassMap());
        $this->assertEquals(RootClassConstructor::class, $config->mapClassName(RootClassConstructor::class));
        $this->assertEquals(RootClassInterface::class, $config->mapClassName(RootClassInterface::class));
    }
}
