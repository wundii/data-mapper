<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Reflection;

final readonly class UseStatementsReflection
{
    /**
     * @param UseStatementReflection[] $useStatements
     */
    public function __construct(
        private ?string $namespaceName,
        private array $useStatements,
    ) {
    }

    public function getNamespaceName(): ?string
    {
        return $this->namespaceName;
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
            if (strcasecmp($useStatement->getAs(), $search) === 0) {
                return $useStatement->getClass();
            }
        }

        if ($this->namespaceName !== null) {
            $search = $this->namespaceName . '\\' . $search;

            foreach ($this->useStatements as $useStatement) {
                if (strcasecmp($useStatement->getClass(), $search) === 0) {
                    return $useStatement->getClass();
                }
            }

            if (class_exists($search)) {
                return $search;
            }
        }

        return null;
    }
}
