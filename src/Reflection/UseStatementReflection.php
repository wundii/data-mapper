<?php

declare(strict_types=1);

namespace DataMapper\Reflection;

final readonly class UseStatementReflection
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
