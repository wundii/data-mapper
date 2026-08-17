<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Parser;

use ReflectionClass;
use Wundii\DataMapper\Dto\UseStatementDto;
use Wundii\DataMapper\Dto\UseStatementsDto;
use Wundii\DataMapper\Exception\DataMapperException;

/**
 * @template T of object
 * @extends AbstractReflectionParser<T>
 */
class ReflectionUseParser extends AbstractReflectionParser
{
    /**
     * @var array<string, UseStatementsDto|null>
     */
    private static array $useStatementsCache = [];

    public function basename(string $classString): string
    {
        return basename(str_replace('\\', '/', $classString));
    }

    /**
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
        $groupPrefix = '';

        foreach (token_get_all($fileContent) as $token) {
            if (is_array($token)) {
                /**
                 * Everything relevant is declared before the type itself,
                 * a `use` inside the body imports a trait, not a class.
                 */
                if (in_array($token[0], [T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM], true)) {
                    break;
                }

                switch ($token[0]) {
                    case T_USE:
                        $useStatement = '';
                        $groupPrefix = '';
                        break;
                    case T_FUNCTION:
                    case T_CONST:
                        $useStatement = null;
                        break;
                    case T_NAME_QUALIFIED:
                    case T_STRING:
                        if ($useStatement !== null) {
                            $useStatement .= $token[1];
                        }

                        break;
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
                }

                continue;
            }

            if ($useStatement === null) {
                continue;
            }

            switch ($token) {
                case '{':
                    $groupPrefix = $useStatement;
                    $useStatement = '';
                    break;
                case ',':
                case '}':
                    if ($useStatement !== '') {
                        $useStatements[] = $this->createUseStatement($groupPrefix . $useStatement);
                    }

                    $useStatement = '';

                    if ($token === '}') {
                        $groupPrefix = '';
                    }

                    break;
                case ';':
                    if ($useStatement !== '') {
                        $useStatements[] = $this->createUseStatement($groupPrefix . $useStatement);
                    }

                    $useStatement = null;
                    $groupPrefix = '';
                    break;
            }
        }

        return new UseStatementsDto(
            $reflectionClass->getNamespaceName() ?: null,
            $useStatements,
        );
    }

    /**
     * @param class-string<T>|T $objectOrClass
     * @throws DataMapperException
     */
    public function parse(object|string $objectOrClass): ?UseStatementsDto
    {
        if (! is_object($objectOrClass) && ! class_exists($objectOrClass) && ! interface_exists($objectOrClass)) {
            throw DataMapperException::InvalidArgument(sprintf('object %s does not exist', $objectOrClass));
        }

        $classString = is_object($objectOrClass) ? $objectOrClass::class : $objectOrClass;

        if (array_key_exists($classString, self::$useStatementsCache)) {
            return self::$useStatementsCache[$classString];
        }

        $reflectionClass = $this->reflectionClassCache($objectOrClass);

        if ($reflectionClass->isInternal()) {
            return self::$useStatementsCache[$classString] = null;
        }

        if ($reflectionClass->getFileName() === false) {
            throw DataMapperException::Error('Could not get file name from ' . $classString);
        }

        return self::$useStatementsCache[$classString] = $this->parseToken($reflectionClass);
    }

    public static function clearUseStatementsCache(): void
    {
        self::$useStatementsCache = [];
    }

    private function createUseStatement(string $useStatement): UseStatementDto
    {
        $classString = $useStatement;
        $alias = null;

        if (str_contains($useStatement, ' as ')) {
            [$classString, $alias] = explode(' as ', $useStatement);
        }

        return new UseStatementDto($classString, $alias ?? $this->basename($classString));
    }
}
