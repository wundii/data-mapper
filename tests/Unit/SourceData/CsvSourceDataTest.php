<?php

declare(strict_types=1);

namespace Unit\SourceData;

use Integration\Objects\Types\TypeString;
use PHPUnit\Framework\TestCase;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\Dto\CsvDto;
use Wundii\DataMapper\Enum\SourceTypeEnum;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\SourceData\CsvSourceData;

class CsvSourceDataTest extends TestCase
{
    public function getCsvSourceData($source = 'empty'): CsvSourceData
    {
        return new CsvSourceData(
            new DataConfig(),
            $source,
            TypeString::class,
        );
    }

    public function testWrongSourceValue(): void
    {
        $this->expectException(DataMapperException::class);
        $this->expectExceptionMessage('The ' . SourceTypeEnum::CSV->value . ' source is not from type CsvDto');

        $csvSourceData = $this->getCsvSourceData();
        $csvSourceData->resolve();
    }

    public function testWrongHeaderLineGreaterFirstLine(): void
    {
        $this->expectException(DataMapperException::class);
        $this->expectExceptionMessage('The header line (1) must be before the first data line (0) in ' . SourceTypeEnum::CSV->value . ' source.');

        $csvDto = new CsvDto(
            'source.csv',
            CsvDto::DEFAULT_SEPARATOR,
            CsvDto::DEFAULT_ENCLOSURE,
            CsvDto::DEFAULT_ESCAPE,
            2,
            1,
        );

        $csvSourceData = $this->getCsvSourceData($csvDto);
        $csvSourceData->resolve();
    }

    public function testIsFileTrue(): void
    {
        $csvSourceData = $this->getCsvSourceData();
        $isFile = $csvSourceData->isFile(__FILE__);

        $this->assertTrue($isFile, 'Expected isFile to return true for an existing file.');
    }

    public function testIsFileFalse(): void
    {
        $csv = "string\n" .
            "Nostromo\n" .
            'Weyland-Yutani';

        $csvSourceData = $this->getCsvSourceData();
        $isFile = $csvSourceData->isFile($csv);

        $this->assertFalse($isFile, 'Expected isFile to return false for a string content.');
    }

    public function testIsFileWrong(): void
    {
        $csvSourceData = $this->getCsvSourceData();
        $isFile = $csvSourceData->isFile('/path/to/nonexistent/file.csv');

        $this->assertFalse($isFile, 'Expected isFile to return false for a non-existent file.');
    }
}
