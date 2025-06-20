<?php

declare(strict_types=1);

namespace Unit\Resolver;

use Exception;
use MockClasses\ItemConstructor;
use MockClasses\RootProperties;
use MockClasses\RootSetters;
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
use Wundii\DataMapper\Resolver\ArrayDtoResolver;

class ArrayDtoResolverTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testMatchExceptionWithoutInterface()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('TypeDtoInterface not implemented: MockClasses\TypeDto');

        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $typeDto = new TypeDto();

        $arrayDtoResolver = new ArrayDtoResolver();
        $arrayDtoResolver->matchValue($dataConfig, $typeDto);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataBool()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $typeDto = new BoolDto(true, 'destination');

        $arrayDtoResolver = new ArrayDtoResolver();
        $result = $arrayDtoResolver->matchValue($dataConfig, $typeDto);

        $this->assertTrue($result);

        $typeDto = new BoolDto(false, 'destination');

        $arrayDtoResolver = new ArrayDtoResolver();
        $result = $arrayDtoResolver->matchValue($dataConfig, $typeDto);

        $this->assertFalse($result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataInt()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $typeDto = new IntDto(11, 'destination');

        $arrayDtoResolver = new ArrayDtoResolver();
        $result = $arrayDtoResolver->matchValue($dataConfig, $typeDto);

        $this->assertSame(11, $result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataFloat()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $typeDto = new FloatDto(22.2, 'destination');

        $arrayDtoResolver = new ArrayDtoResolver();
        $result = $arrayDtoResolver->matchValue($dataConfig, $typeDto);

        $this->assertSame(22.2, $result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataString()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $typeDto = new StringDto('Value', 'destination');

        $arrayDtoResolver = new ArrayDtoResolver();
        $result = $arrayDtoResolver->matchValue($dataConfig, $typeDto);

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

        $arrayDtoResolver = new ArrayDtoResolver();
        $result = $arrayDtoResolver->matchValue($dataConfig, $typeDto);

        $this->assertSame(['value1', false], $result);
    }

    /**
     * @throws Exception
     */
    public function testMatchDataObjectConstructor()
    {
        $dataConfig = new DataConfig(
            approachEnum: ApproachEnum::CONSTRUCTOR
        );
        $typeDto = new ObjectDto(
            'MockClasses\ItemConstructor',
            [
                new FloatDto(4.4, 'price'),
                new BoolDto(false, 'isAvailable'),
            ],
            'destination',
        );

        $arrayDtoResolver = new ArrayDtoResolver();
        $result = $arrayDtoResolver->matchValue($dataConfig, $typeDto);

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

        $arrayDtoResolver = new ArrayDtoResolver();
        $result = $arrayDtoResolver->matchValue($dataConfig, $typeDto);

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

        $arrayDtoResolver = new ArrayDtoResolver();
        $result = $arrayDtoResolver->matchValue($dataConfig, $typeDto);

        $expected = new RootSetters();
        $expected->setId(4);
        $expected->setName('test');

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws Exception
     */
    public function testResolveDataArrayEmpty()
    {
        $dataConfig = new DataConfig(ApproachEnum::CONSTRUCTOR);
        $typeDto = new ArrayDto([]);

        $arrayDtoResolver = new ArrayDtoResolver();
        $result = $arrayDtoResolver->resolve($dataConfig, $typeDto);

        $this->assertEquals([], $result);
    }

    /**
     * @throws Exception
     */
    public function testResolveDataArray()
    {
        $dataConfig = new DataConfig(
            approachEnum: ApproachEnum::CONSTRUCTOR
        );
        $typeDto = new ArrayDto([
            new StringDto('value1', 'destination1'),
            new BoolDto(false, 'destination2'),
            new IntDto(1, 'destination3'),
            new FloatDto(2.2, 'destination4'),
            new ArrayDto([
                new StringDto('value2', 'destination5'),
                new BoolDto(true, 'destination6'),
            ], 'destination7'),
            new ObjectDto(
                'MockClasses\ItemConstructor',
                [
                    new FloatDto(2.2, 'price'),
                    new BoolDto(true, 'isAvailable'),
                ],
                'destination5',
            ),
        ]);

        $arrayDtoResolver = new ArrayDtoResolver();
        $result = $arrayDtoResolver->resolve($dataConfig, $typeDto);

        $expected = [
            'value1',
            false,
            1,
            2.2,
            [
                'value2',
                true,
            ],
            new ItemConstructor(2.2, true),
        ];

        $this->assertEquals($expected, $result);
    }
}
