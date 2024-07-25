<?php

declare(strict_types=1);

namespace DataMapper\Resolver;

use DataMapper\Reflection\UseStatementReflection;
use DataMapper\Reflection\UseStatementsReflection;
use Exception;
use InvalidArgumentException;
use ReflectionClass;

final readonly class ReflectionTokenResolver
{
    public function basename(string $classString): string
    {
        return basename(str_replace('\\', '/', $classString));
    }

    /**
     * @template T of object
     * @param ReflectionClass<T> $reflectionClass
     * @throws Exception
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
            throw new Exception('Could not read file content from ' . $reflectionClass->getFileName());
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
     * @throws Exception
     */
    public function resolve(string|object $object): UseStatementsReflection
    {
        if (! is_object($object) && ! class_exists($object) && ! interface_exists($object)) {
            throw new InvalidArgumentException(sprintf('object %s does not exist', $object));
        }

        $reflectionClass = new ReflectionClass($object);

        /**
         * @todo unittest
         */
        if ($reflectionClass->isInternal()) {
            return new UseStatementsReflection(null, []);
        }

        if ($reflectionClass->getFileName() === false) {
            $classString = is_object($object) ? $object::class : $object;
            throw new Exception('Could not get file name from ' . $classString);
        }

        return $this->parseToken($reflectionClass);
    }
}
