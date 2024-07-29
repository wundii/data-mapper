<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use ReflectionClass;
use ReflectionException;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Reflection\UseStatementReflection;
use Wundii\DataMapper\Reflection\UseStatementsReflection;

final readonly class ReflectionTokenResolver
{
    public function basename(string $classString): string
    {
        return basename(str_replace('\\', '/', $classString));
    }

    /**
     * @template T of object
     * @param ReflectionClass<T> $reflectionClass
     * @throws DataMapperException
     */
    public function parseToken(ReflectionClass $reflectionClass): UseStatementsReflection
    {
        $useStatements = [];
        if ($reflectionClass->getName() !== null) {
            $useStatements[] = new UseStatementReflection(
                $reflectionClass->getName(),
                $this->basename($reflectionClass->getName())
            );
        }

        $fileContent = file_get_contents($reflectionClass->getFileName() ?: '');
        if ($fileContent === false) {
            throw DataMapperException::Error('Could not read file content from ' . $reflectionClass->getFileName());
        }

        $useStatement = null;
        foreach (token_get_all($fileContent) as $token) {
            if ($token[0] === T_CLASS) {
                break;
            }

            /**
             * cases with int value are a fix,
             * because the defined constants are not correctly,
             * I could not find the why
             */
            switch ($token[0]) {
                case 318: // T_USE
                case T_USE:
                    $useStatement = '';
                    break;
                case 265: // T_STRING
                case T_NAME_QUALIFIED:
                case T_STRING:
                    if ($useStatement !== null) {
                        $useStatement .= $token[1];
                    }

                    break;
                case 301: // T_AS
                case T_AS:
                    if ($useStatement !== null) {
                        $useStatement .= ' as ';
                    }

                    break;
                case T_NS_SEPARATOR:
                    if ($useStatement !== null) {
                        $useStatement .= '\\';
                    }

                    break;
                case ';':
                    if ($useStatement !== null) {
                        $classString = $useStatement;
                        $alias = null;

                        if (str_contains($useStatement, ' as ')) {
                            list($classString, $alias) = explode(' as ', $useStatement);
                        }

                        $useStatements[] = new UseStatementReflection($classString, $alias ?? $this->basename($classString));
                        $useStatement = null;
                    }

                    break;
            }
        }

        return new UseStatementsReflection(
            $reflectionClass->getNamespaceName() ?: null,
            $useStatements,
        );
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    public function resolve(string|object $object): UseStatementsReflection
    {
        if (! is_object($object) && ! class_exists($object) && ! interface_exists($object)) {
            throw DataMapperException::InvalidArgument(sprintf('object %s does not exist', $object));
        }

        $reflectionClass = new ReflectionClass($object);

        if ($reflectionClass->isInternal()) {
            return new UseStatementsReflection(null, []);
        }

        if ($reflectionClass->getFileName() === false) {
            $classString = is_object($object) ? $object::class : $object;
            throw DataMapperException::Error('Could not get file name from ' . $classString);
        }

        return $this->parseToken($reflectionClass);
    }
}
