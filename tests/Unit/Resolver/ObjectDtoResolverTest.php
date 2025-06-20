<?php

declare(strict_types=1);

namespace Unit\Resolver;

use Exception;
use MockClasses\ItemConstructor;
use MockClasses\RootProperties;
use MockClasses\RootSetters;
use MockClasses\TestEnum;
use MockClasses\TestStringEnum;
use MockClasses\TypeDto;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\Dto\Type\ArrayDto;
use Wundii\DataMapper\Dto\Type\BoolDto;
use Wundii\DataMapper\Dto\Type\FloatDto;
use Wundii\DataMapper\Dto\Type\IntDto;
use Wundii\DataMapper\Dto\Type\ObjectDto;
use Wundii\DataMapper\Dto\Type\StringDto;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Resolver\ObjectDtoResolver;

class ObjectDtoResolverTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreateInstanceConstructor(): void
    {
        $dataConfig = new DataConfig(
            approachEnum: ApproachEnum::CONSTRUCTOR,
        );
        $typeDto = new ObjectDto(
            'MockClasses\ItemConstructor',
            [],
            'destination',
        );

        $objectDtoResolver = new ObjectDtoResolver();
        $return = $objectDtoResolver->createInstance($dataConfig, $typeDto, [5.67, true]);

        $expected = new ItemConstructor(5.67, true);

        $this->assertInstanceOf(ItemConstructor::class, $return);
        $this->assertEquals($expected, $return);
    }

    /**
     * @throws Exception
     */
    public function testCreateInstanceProperty(): void
    {
        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        $typeDto = new ObjectDto(
            'MockClasses\RootProperties',
            [],
            'destination',
        );

        $objectDtoResolver = new ObjectDtoResolver();
        $return = $objectDtoResolver->createInstance($dataConfig, $typeDto);
        $this->assertInstanceOf(RootProperties::class, $return);
    }

    /**
     * @throws Exception
     */
    public function testCreateInstanceSetter(): void
    {
        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $typeDto = new ObjectDto(
            'MockClasses\RootSetters',
            [],
            'destination',
        );

        $objectDtoResolver = new ObjectDtoResolver();
        $return = $objectDtoResolver->createInstance($dataConfig, $typeDto);
        $this->assertInstanceOf(RootSetters::class, $return);
    }

    /**
     * @throws Exception
     */
    public function testCreateInstanceEnumException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Enum class must have a from method: ' . TestEnum::class);

        $dataConfig = new DataConfig(approachEnum: ApproachEnum::PROPERTY);
        $typeDto = new ObjectDto(
            'MockClasses\TestEnum',
            [
                new StringDto('two', 'destination'),
            ],
            'destination',
            true
        );

        $objectDtoResolver = new ObjectDtoResolver();
        $objectDtoResolver->createInstance($dataConfig, $typeDto, ['two']);
    }

    /**
     * @throws Exception
     */
    public function testCreateInstanceEnum(): void
    {
        $dataConfig = new DataConfig(approachEnum: ApproachEnum::PROPERTY);
        $typeDto = new ObjectDto(
            'MockClasses\TestStringEnum',
            [
                new StringDto('two', 'destination'),
            ],
            'destination',
            true
        );

        $expected = TestStringEnum::TWO;

        $objectDtoResolver = new ObjectDtoResolver();
        $return = $objectDtoResolver->createInstance($dataConfig, $typeDto, ['two']);
        $this->assertInstanceOf(TestStringEnum::class, $return);
        $this->assertEquals($expected, $return);
    }

    /**
     * @throws Exception
     */
    public function testMatchExceptionWithoutInterface()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('TypeDtoInterface not implemented: MockClasses\TypeDto');

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $typeDto = new TypeDto();

        $objectDtoResolver = new ObjectDtoResolver();
        $objectDtoResolver->matchValue($dataConfig, $typeDto);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataBool()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $typeDto = new BoolDto(true, 'destination');

        $objectDtoResolver = new ObjectDtoResolver();
        $result = $objectDtoResolver->matchValue($dataConfig, $typeDto);

        $this->assertTrue($result);

        $typeDto = new BoolDto(false, 'destination');

        $objectDtoResolver = new ObjectDtoResolver();
        $result = $objectDtoResolver->matchValue($dataConfig, $typeDto);

        $this->assertFalse($result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataInt()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $typeDto = new IntDto(11, 'destination');

        $objectDtoResolver = new ObjectDtoResolver();
        $result = $objectDtoResolver->matchValue($dataConfig, $typeDto);

        $this->assertSame(11, $result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataFloat()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $typeDto = new FloatDto(22.2, 'destination');

        $objectDtoResolver = new ObjectDtoResolver();
        $result = $objectDtoResolver->matchValue($dataConfig, $typeDto);

        $this->assertSame(22.2, $result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataString()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $typeDto = new StringDto('Value', 'destination');

        $objectDtoResolver = new ObjectDtoResolver();
        $result = $objectDtoResolver->matchValue($dataConfig, $typeDto);

        $this->assertSame('Value', $result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataArray()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $typeDto = new ArrayDto([
            new StringDto('value1', 'destination1'),
            new BoolDto(false, 'destination2'),
        ], 'destination');

        $objectDtoResolver = new ObjectDtoResolver();
        $result = $objectDtoResolver->matchValue($dataConfig, $typeDto);

        $this->assertSame(['value1', false], $result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataObjectConstructor()
    {
        $dataConfig = new DataConfig(
            approachEnum: ApproachEnum::CONSTRUCTOR,
        );
        $typeDto = new ObjectDto(
            'MockClasses\ItemConstructor',
            [
                new FloatDto(4.4, 'price'),
                new BoolDto(false, 'isAvailable'),
            ],
            'destination',
        );

        $objectDtoResolver = new ObjectDtoResolver();
        $result = $objectDtoResolver->matchValue($dataConfig, $typeDto);

        $this->assertEquals(new ItemConstructor(4.4, false), $result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataObjectProperty()
    {
        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        $typeDto = new ObjectDto(
            'MockClasses\RootProperties',
            [
                new IntDto(4, 'id'),
                new StringDto('test', 'name'),
            ],
            'destination',
        );

        $objectDtoResolver = new ObjectDtoResolver();
        $result = $objectDtoResolver->matchValue($dataConfig, $typeDto);

        $expected = new RootProperties();
        $expected->name = 'test';
        $expected->id = 4;

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataObjectSetter()
    {
        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $typeDto = new ObjectDto(
            'MockClasses\RootSetters',
            [
                new IntDto(4, 'setId'),
                new StringDto('test', 'setName'),
            ],
            'destination',
        );

        $objectDtoResolver = new ObjectDtoResolver();
        $result = $objectDtoResolver->matchValue($dataConfig, $typeDto);

        $expected = new RootSetters();
        $expected->setId(4);
        $expected->setName('test');

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws Exception
     */
    public function testResolveConstructorException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('You can not use constructor approach with an object');

        $dataConfig = new DataConfig(
            approachEnum: ApproachEnum::CONSTRUCTOR,
        );
        $typeDto = new ObjectDto(
            new ItemConstructor(4.4, false),
            [
                new FloatDto(4.4, 'price'),
                new BoolDto(false, 'isAvailable'),
            ],
            'destination',
        );

        $objectDtoResolver = new ObjectDtoResolver();
        $objectDtoResolver->resolve($dataConfig, $typeDto);
    }

    /**
     * @throws Exception
     */
    public function testResolveConstructor()
    {
        $dataConfig = new DataConfig(
            approachEnum: ApproachEnum::CONSTRUCTOR,
        );
        $typeDto = new ObjectDto(
            'MockClasses\ItemConstructor',
            [
                new FloatDto(4.4, 'price'),
                new BoolDto(false, 'isAvailable'),
            ],
            'destination',
        );

        $objectDtoResolver = new ObjectDtoResolver();
        $result = $objectDtoResolver->resolve($dataConfig, $typeDto);

        $this->assertEquals(new ItemConstructor(4.4, false), $result);
    }

    /**
     * @throws Exception
     */
    public function testResolvePropertyException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Destination is not declared');

        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        $typeDto = new ObjectDto(
            'MockClasses\RootProperties',
            [
                new IntDto(4),
                new StringDto('test'),
            ],
            'destination',
        );

        $objectDtoResolver = new ObjectDtoResolver();
        $objectDtoResolver->resolve($dataConfig, $typeDto);
    }

    /**
     * @throws Exception
     */
    public function testResolvePropertyWithClassString()
    {
        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        $typeDto = new ObjectDto(
            'MockClasses\RootProperties',
            [
                new IntDto(4, 'id'),
                new StringDto('test', 'name'),
            ],
            'destination',
        );

        $objectDtoResolver = new ObjectDtoResolver();
        $result = $objectDtoResolver->resolve($dataConfig, $typeDto);

        $expected = new RootProperties();
        $expected->name = 'test';
        $expected->id = 4;

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws Exception
     */
    public function testResolvePropertyWithObject()
    {
        $object = new RootProperties();
        $object->id = 999;

        $dataConfig = new DataConfig(ApproachEnum::PROPERTY);
        $typeDto = new ObjectDto(
            $object,
            [
                new IntDto(4, 'id'),
                new StringDto('test', 'name'),
            ],
            'destination',
        );

        $objectDtoResolver = new ObjectDtoResolver();
        $result = $objectDtoResolver->resolve($dataConfig, $typeDto);

        $expected = new RootProperties();
        $expected->name = 'test';
        $expected->id = 4;

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws Exception
     */
    public function testResolveSetterException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Destination is not declared');

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $typeDto = new ObjectDto(
            'MockClasses\RootSetters',
            [
                new IntDto(4),
                new StringDto('test'),
            ],
            'destination',
        );

        $objectDtoResolver = new ObjectDtoResolver();
        $objectDtoResolver->resolve($dataConfig, $typeDto);
    }

    /**
     * @throws Exception
     */
    public function testResolveSetterWithClassString()
    {
        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $typeDto = new ObjectDto(
            'MockClasses\RootSetters',
            [
                new IntDto(4, 'setId'),
                new StringDto('test', 'setName'),
            ],
            'destination',
        );

        $objectDtoResolver = new ObjectDtoResolver();
        $result = $objectDtoResolver->resolve($dataConfig, $typeDto);

        $expected = new RootSetters();
        $expected->setId(4);
        $expected->setName('test');

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws Exception
     */
    public function testResolveSetterWithObject()
    {
        $object = new RootSetters();
        $object->setId(999);

        $dataConfig = new DataConfig(ApproachEnum::SETTER);
        $typeDto = new ObjectDto(
            $object,
            [
                new IntDto(4, 'setId'),
                new StringDto('test', 'setName'),
            ],
            'destination',
        );

        $objectDtoResolver = new ObjectDtoResolver();
        $result = $objectDtoResolver->resolve($dataConfig, $typeDto);

        $expected = new RootSetters();
        $expected->setId(4);
        $expected->setName('test');

        $this->assertEquals($expected, $result);
    }
}
