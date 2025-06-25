<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use ReflectionClass;
use ReflectionException;
use Wundii\DataMapper\Dto\UseStatementDto;
use Wundii\DataMapper\Dto\UseStatementsDto;
use Wundii\DataMapper\Exception\DataMapperException;


class ReflectionUseResolver extends AbstractReflectionClassResolver
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
    public function parseToken(ReflectionClass $reflectionClass): UseStatementsDto
    {
        $useStatements = [
            new UseStatementDto(
                $reflectionClass->getName(),
                $this->basename($reflectionClass->getName())
            ),
        ];

        if (file_exists((string) $reflectionClass->getFileName()) === false) {
            throw DataMapperException::Error('File not found: ' . $reflectionClass->getFileName());
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

                        $useStatements[] = new UseStatementDto($classString, $alias ?? $this->basename($classString));
                        $useStatement = null;
                    }

                    break;
            }
        }

        return new UseStatementsDto(
            $reflectionClass->getNamespaceName() ?: null,
            $useStatements,
        );
    }

    /**
     * @throws DataMapperException
     */
    public function resolve(object|string $object): ?UseStatementsDto
    {
        if (! is_object($object) && ! class_exists($object) && ! interface_exists($object)) {
            throw DataMapperException::InvalidArgument(sprintf('object %s does not exist', $object));
        }

        $reflectionClass = $this->reflectionClassCache($object);

        if ($reflectionClass->isInternal()) {
            return null;
        }

        if ($reflectionClass->getFileName() === false) {
            $classString = is_object($object) ? $object::class : $object;
            throw DataMapperException::Error('Could not get file name from ' . $classString);
        }

        return $this->parseToken($reflectionClass);
    }

    /**
     * @throws ReflectionException
     * @throws DataMapperException
     */
    public static function resolveObject(object|string $objectOrClass): ?UseStatementsDto
    {
        return (new self())->resolve($objectOrClass);
    }
}
