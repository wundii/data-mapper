<?php

declare(strict_types=1);

namespace Integration;

use Integration\Objects\UnknownAttribute\UnknownAttributeConstructor;
use Integration\Objects\UnknownAttribute\UnknownAttributeProperty;
use Integration\Objects\UnknownAttribute\UnknownAttributeSetter;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\DataMapper;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ApproachEnum;

class UnknownAttributeTest extends TestCase
{
    public function testAttributeClassesDoNotExist(): void
    {
        $this->assertFalse(class_exists('Vendor\NotInstalled\Attribute\Description'));
        $this->assertFalse(class_exists('Vendor\NotInstalled\Attribute\Documentation'));
    }

    public function testConstructorIgnoresUnknownAttributes(): void
    {
        $dataMapper = new DataMapper(new DataConfig(ApproachEnum::CONSTRUCTOR));
        $return = $dataMapper->json('{"name":"afterbuy","count":3}', UnknownAttributeConstructor::class);

        $this->assertEquals(new UnknownAttributeConstructor('afterbuy', 3), $return);
    }

    public function testSetterIgnoresUnknownAttributes(): void
    {
        $dataMapper = new DataMapper(new DataConfig(ApproachEnum::SETTER));
        $return = $dataMapper->xml('<root><name>afterbuy</name><count>3</count></root>', UnknownAttributeSetter::class);

        $this->assertInstanceOf(UnknownAttributeSetter::class, $return);
        $this->assertSame('afterbuy', $return->getName());
        $this->assertSame(3, $return->getCount());
    }

    public function testPropertyIgnoresUnknownAttributes(): void
    {
        $dataMapper = new DataMapper(new DataConfig(ApproachEnum::PROPERTY, AccessibleEnum::PUBLIC));
        $return = $dataMapper->array([
            'name' => 'afterbuy',
            'count' => 3,
        ], UnknownAttributeProperty::class);

        $this->assertInstanceOf(UnknownAttributeProperty::class, $return);
        $this->assertSame('afterbuy', $return->name);
        $this->assertSame(3, $return->count);
    }
}
