<?php

declare(strict_types=1);

namespace Unit;

use InvalidArgumentException;
use MockClasses\RootConstructor;
use MockClasses\RootInterface;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ApproachEnum;

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
            1 => RootConstructor::class,
        ]);
    }

    public function testDataConfigClassExceptionNoStringValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The class map must contain only strings');

        new DataConfig(classMap: [
            RootConstructor::class => 1,
        ]);
    }

    public function testDataConfigClassExceptionKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The key class does not exist');

        new DataConfig(classMap: [
            'failKey' => RootConstructor::class,
        ]);
    }

    public function testDataConfigClassExceptionValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value class does not exist');

        new DataConfig(classMap: [
            RootInterface::class => 'failValue',
        ]);
    }

    public function testDataConfigCustom(): void
    {
        $config = new DataConfig(
            ApproachEnum::PROPERTY,
            AccessibleEnum::PRIVATE,
            [
                RootInterface::class => RootConstructor::class,
            ]
        );

        $this->assertInstanceOf(DataConfig::class, $config);
        $this->assertEquals(ApproachEnum::PROPERTY, $config->getApproach());
        $this->assertEquals(AccessibleEnum::PRIVATE, $config->getAccessible());
        $this->assertIsArray($config->getClassMap());
        $this->assertCount(1, $config->getClassMap());
        $this->assertArrayHasKey(RootInterface::class, $config->getClassMap());
        $this->assertEquals(RootConstructor::class, $config->getClassMap()[RootInterface::class]);
        $this->assertEquals(RootConstructor::class, $config->mapClassName(RootInterface::class));

        $config = new DataConfig(
            ApproachEnum::PROPERTY,
            AccessibleEnum::PRIVATE,
            [
                RootConstructor::class => RootConstructor::class,
            ]
        );
        $this->assertInstanceOf(DataConfig::class, $config);
        $this->assertArrayHasKey(RootConstructor::class, $config->getClassMap());
        $this->assertEquals(RootConstructor::class, $config->mapClassName(RootConstructor::class));
        $this->assertEquals(RootInterface::class, $config->mapClassName(RootInterface::class));
    }
}
