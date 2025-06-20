<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto\Type;

use Wundii\DataMapper\Interface\ValueDtoInterface;

final readonly class FloatDto implements ValueDtoInterface
{
    public function __construct(
        private float|string $value,
        private ?string $destination = null,
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->stringToFloat();
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function getValue(): float
    {
        return filter_var($this->value, FILTER_VALIDATE_FLOAT) ?: $this->stringToFloat();
    }

    public function stringToFloat(): float
    {
        $number = (string) $this->value;
        $number = (string) preg_replace('#[^-0-9,.]#', '', $number);

        if (str_contains($number, ',') && str_contains($number, '.') && strpos($number, ',') < strpos($number, '.')) {
            $number = str_replace(',', '', $number);
        } elseif (str_contains($number, ',') && str_contains($number, '.') && strpos($number, ',') > strpos($number, '.')) {
            $number = str_replace(',', '_', $number);
            $number = str_replace('.', '', $number);
            $number = str_replace('_', '.', $number);
        } else {
            $number = str_replace(',', '.', $number);
        }

        return (float) $number;
    }
}
