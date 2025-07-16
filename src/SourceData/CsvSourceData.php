<?php

declare(strict_types=1);

namespace Wundii\DataMapper\SourceData;

use ReflectionException;
use SplFileObject;
use Wundii\DataMapper\Dto\CsvDto;
use Wundii\DataMapper\Enum\SourceTypeEnum;
use Wundii\DataMapper\Exception\DataMapperException;

/**
 * @template T of object
 * @extends AbstractSourceData<T>
 */
final class CsvSourceData extends AbstractSourceData
{
    public const SOURCE_TYPE = SourceTypeEnum::CSV;

    public function isFile(string $filename): bool
    {
        $filename = preg_replace('/\r\n|\r|\n/', '', $filename);
        $filename = substr((string) $filename, 0, 500);
        return is_file($filename);
    }

    /**
     * @return T|T[]
     * @throws DataMapperException|ReflectionException
     */
    public function resolve(): object|array
    {
        $sourceTypeEnum = self::SOURCE_TYPE;
        $isContent = false;

        if (! $this->source instanceof CsvDto) {
            throw DataMapperException::Error(sprintf('The %s source is not from type CsvDto', $sourceTypeEnum->value));
        }

        if ($this->source->getHeaderLine() >= $this->source->getFirstLine()) {
            throw DataMapperException::Error(
                sprintf(
                    'The header line (%d) must be before the first data line (%d) in %s source.',
                    $this->source->getHeaderLine(),
                    $this->source->getFirstLine(),
                    $sourceTypeEnum->value
                )
            );
        }

        $source = $this->source->getSource();
        if (! $this->isFile($source)) {
            $isContent = true;
            $tempPath = tempnam(sys_get_temp_dir(), 'CSV_');

            if (! $tempPath || ! file_put_contents($tempPath, $source)) {
                throw DataMapperException::Error(sprintf('The file "%s" could not be written', $source));
            }

            $source = $tempPath;
        }

        if (! file_exists($source)) {
            throw DataMapperException::Error(sprintf('The file "%s" could not be read', $source));
        }

        $csv = new SplFileObject($source);
        $csv->setFlags(SplFileObject::READ_CSV);
        $csv->setCsvControl(
            $this->source->getSeparator(),
            $this->source->getEnclosure(),
            $this->source->getEscape(),
        );

        $csv->rewind();
        $csv->seek($this->source->getHeaderLine());

        /** @var string[] $header */
        $header = (array) $csv->current();
        $headerCnt = count($header);
        $csvArray = [];

        for ($csv->seek($this->source->getFirstLine()); ! $csv->eof(); $csv->next()) {
            $row = (array) $csv->current();

            if (count($row) !== $headerCnt) {
                continue;
            }

            /**
             * Skip empty rows at the end of the file.
             */
            /** @phpstan-ignore-next-line bug, Left side of && is always false. ->oef() can be false or true*/
            if ($csv->eof() && $row === [null]) {
                continue;
            }

            $csvArray[] = array_combine($header, $row);

        }

        if ($isContent) {
            unlink($source);
        }

        $arraySourceData = new ArraySourceData(
            $this->dataConfig,
            $csvArray,
            $this->objectOrClass,
            $this->rootElementTree,
            $this->forceInstance,
        );

        return $arraySourceData->resolve(self::SOURCE_TYPE);
    }
}
