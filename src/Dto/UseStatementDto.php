<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

final readonly class UseStatementDto
{
    public function __construct(
        private string $class,
        private string $as
    ) {
    }

    public function getAs(): string
    {
        return $this->as;
    }

    public function getClass(): string
    {
        return $this->class;
    }
}
