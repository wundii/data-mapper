<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use Wundii\DataMapper\Dto\AnnotationDto;
use Wundii\DataMapper\Dto\ParameterDto;
use Wundii\DataMapper\Dto\UseStatementsDto;

class ReflectionAnnotationResolver
{
    public function __construct(
        private ?UseStatementsDto $useStatementsDto,
    ) {
    }

    /**
     * @param string[] $types
     * @return string[]
     */
    public function completeClassStrings(array $types): array
    {
        if (! $this->useStatementsDto instanceof UseStatementsDto) {
            return $types;
        }

        foreach ($types as $key => $type) {
            if (class_exists($type)) {
                continue;
            }

            $classString = $this->useStatementsDto->findClassString($type);

            if ($classString !== null) {
                $types[$key] = $classString;
            }

            if (! str_ends_with($type, '[]')) {
                continue;
            }

            $classString = substr($type, 0, -2);
            if (class_exists($classString)) {
                continue;
            }

            $classString = $this->useStatementsDto->findClassString($classString);

            if ($classString !== null) {
                $types[$key] = $classString . '[]';
            }
        }

        return $types;
    }

    public function resolve(false|string $docComment): ?AnnotationDto
    {
        if ($docComment === false || $docComment === '') {
            return null;
        }

        $parameterReflections = [];
        $variables = [];
        $docComment = trim($docComment);

        if (! str_starts_with($docComment, '/**')) {
            return null;
        }

        $docComment = substr($docComment, 3, -2);

        $pattern = '/@(?P<name>[A-Za-z_-]+)(?:[ \t]+(?P<value>.*?))?[ \t]*\r?$/m';

        if (preg_match_all($pattern, $docComment, $matches)) {
            $parameters = [];

            /**
             * pre-process for annotation types
             */
            foreach ($matches['name'] as $key => $name) {
                if (strtolower($name) === 'param') {
                    $parameters[] = $matches['value'][$key];
                }

                if (strtolower($name) === 'var') {
                    $variables[] = $matches['value'][$key];
                }
            }

            foreach ($parameters as $param) {
                list($parameterType, $parameter) = explode(' ', $param);

                if (str_starts_with($parameter, '$')) {
                    $parameter = substr($parameter, 1);
                }

                $parameterTypes = explode('|', $parameterType);

                foreach ($parameterTypes as $key => $parameterType) {
                    if (str_starts_with($parameterType, '?')) {
                        $parameterTypes[$key] = 'null';
                        $parameterTypes[] = substr($parameterType, 1);
                    }
                }

                $parameterTypes = $this->completeClassStrings($parameterTypes);

                $parameterReflections[] = new ParameterDto(
                    $parameter,
                    $parameterTypes,
                );
            }

            if ($variables !== []) {
                $variables = explode('|', array_pop($variables));
                foreach ($variables as $key => $variable) {
                    if (str_starts_with($variable, '?')) {
                        $variables[$key] = 'null';
                        $variables[] = substr($variable, 1);
                    }
                }

                $variables = $this->completeClassStrings($variables);
            }
        }

        return new AnnotationDto(
            $parameterReflections,
            $variables,
        );
    }
}
