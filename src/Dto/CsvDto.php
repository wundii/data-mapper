<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

final readonly class CsvDto
{
    public const DEFAULT_SEPARATOR = ',';

    public const DEFAULT_ENCLOSURE = '"';

    public const DEFAULT_ESCAPE = '\\';

    public const DEFAULT_HEADER_LINE = 1;

    public const DEFAULT_FIRST_LINE = 2;

    public function __construct(
        private string $source,
        private string $separator = self::DEFAULT_SEPARATOR,
        private string $enclosure = self::DEFAULT_ENCLOSURE,
        private string $escape = self::DEFAULT_ESCAPE,
        private int $headerLine = self::DEFAULT_HEADER_LINE,
        private int $firstLine = self::DEFAULT_FIRST_LINE,
    ) {
    }

    public function getSource(): string
    {
        return trim($this->source);
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

    public function getEnclosure(): string
    {
        return $this->enclosure;
    }

    public function getEscape(): string
    {
        return $this->escape;
    }

    public function getHeaderLine(): int
    {
        return $this->headerLine > 0 ? $this->headerLine - 1 : 0;
    }

    public function getFirstLine(): int
    {
        return $this->firstLine > 0 ? $this->firstLine - 1 : 0;
    }
}
