<?php

declare(strict_types=1);

namespace DataMapper\Reflection;

final readonly class UseStatementsReflection
{
    /**
     * @param UseStatementReflection[] $useStatements
     */
    public function __construct(
        private array $useStatements,
    ) {
    }

    /**
     * @return UseStatementReflection[]
     */
    public function getUseStatements(): array
    {
        return $this->useStatements;
    }

    public function find(string $search): ?string
    {
        foreach ($this->useStatements as $useStatement) {
            if (strtolower($useStatement->getAs()) === strtolower($search)) {
                return $useStatement->getClass();
            }
        }

        return null;
    }
}
