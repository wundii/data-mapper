<?php

declare(strict_types=1);

namespace Unit\Exception;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Exception\DataMapperInvalidArgumentException;

class DataMapperExceptionTest extends TestCase
{
    public function testExtendConstructor(): void
    {
        $exception = new Exception('Php Internal Exception');
        $dataMapperException = DataMapperException::Error('message', 1, $exception);

        $this->assertInstanceOf(Exception::class, $dataMapperException);
        $this->assertInstanceOf(DataMapperException::class, $dataMapperException);
        $this->assertSame('message', $dataMapperException->getMessage());
        $this->assertSame(1, $dataMapperException->getCode());
        $this->assertSame($exception, $dataMapperException->getPrevious());
    }

    public function testInvalidArgumentWithoutArguments(): void
    {
        $dataMapperException = DataMapperException::InvalidArgument('message');

        $this->assertInstanceOf(Exception::class, $dataMapperException);
        $this->assertInstanceOf(InvalidArgumentException::class, $dataMapperException);
        $this->assertInstanceOf(DataMapperInvalidArgumentException::class, $dataMapperException);
        $this->assertSame('message', $dataMapperException->getMessage());
    }

    public function testInvalidArgumentWithArgumentsString(): void
    {
        $arguments = 'argument';
        $dataMapperException = DataMapperException::InvalidArgument('message', $arguments);

        $this->assertInstanceOf(Exception::class, $dataMapperException);
        $this->assertInstanceOf(InvalidArgumentException::class, $dataMapperException);
        $this->assertInstanceOf(DataMapperInvalidArgumentException::class, $dataMapperException);
        $this->assertSame('message - arguments: argument', $dataMapperException->getMessage());
    }

    public function testInvalidArgumentWithArgumentsArray(): void
    {
        $arguments = [
            'value1',
            'value2',
        ];
        $dataMapperException = DataMapperException::InvalidArgument('message', $arguments);

        $this->assertInstanceOf(Exception::class, $dataMapperException);
        $this->assertInstanceOf(InvalidArgumentException::class, $dataMapperException);
        $this->assertInstanceOf(DataMapperInvalidArgumentException::class, $dataMapperException);
        $this->assertSame('message - arguments: value1, value2', $dataMapperException->getMessage());

        $arguments = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];
        $dataMapperException = DataMapperException::InvalidArgument('message', $arguments);

        $this->assertInstanceOf(Exception::class, $dataMapperException);
        $this->assertInstanceOf(InvalidArgumentException::class, $dataMapperException);
        $this->assertInstanceOf(DataMapperInvalidArgumentException::class, $dataMapperException);
        $this->assertSame('message - arguments: key1: value1, key2: value2', $dataMapperException->getMessage());
    }
}
