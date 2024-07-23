<?php

declare(strict_types=1);

namespace DataMapper\Resolver;

use DataMapper\Reflection\AnnotationReflection;
use DataMapper\Reflection\ObjectReflection;
use DataMapper\Reflection\ParameterReflection;
use DataMapper\Reflection\PropertyReflection;
use DataMapper\Reflection\UseStatementReflection;
use DataMapper\Reflection\UseStatementsReflection;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

final readonly class ReflectionObjectResolver
{
    public function name(ReflectionProperty|ReflectionParameter|ReflectionMethod $reflection): string
    {
        return $reflection->getName();
    }

    public function parseAnnotation(UseStatementsReflection $useStatementsReflection, string $docComment): AnnotationReflection
    {
        $parameterReflections = [];
        $variables = [];
        $docComment = trim($docComment);

        if (! str_starts_with($docComment, '/**')) {
            return new AnnotationReflection([], []);
        }

        $docComment = substr($docComment, 3, -2);

        $pattern = '/@(?P<name>[A-Za-z_-]+)(?:[ \t]+(?P<value>.*?))?[ \t]*\r?$/m';

        if (preg_match_all($pattern, $docComment, $matches)) {
            $parameters = [];

            /**
             * pre-process for annotation types
             */
            foreach ($matches['name'] ?? [] as $key => $name) {
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

                $parameterTypes = $this->completeClassStrings($useStatementsReflection, $parameterTypes);

                $parameterReflections[] = new ParameterReflection(
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

                $variables = $this->completeClassStrings($useStatementsReflection, $variables);
            }
        }

        return new AnnotationReflection(
            $parameterReflections,
            $variables,
        );
    }

    /**
     * @param string[] $types
     * @return string[]
     */
    public function completeClassStrings(UseStatementsReflection $useStatementsReflection, array $types): array
    {
        foreach ($types as $key => $type) {
            if (! str_ends_with($type, '[]')) {
                continue;
            }

            $classString = substr($type, 0, -2);
            if (class_exists($classString)) {
                continue;
            }

            $classString = $useStatementsReflection->find($classString);

            if ($classString !== null) {
                $types[$key] = $classString . '[]';
            }
        }

        return $types;
    }

    public function annotation(UseStatementsReflection $useStatementsReflection, ReflectionProperty|ReflectionFunctionAbstract $property): AnnotationReflection
    {
        $docComment = $property->getDocComment();

        if ($docComment === false) {
            return new AnnotationReflection([], []);
        }

        return $this->parseAnnotation($useStatementsReflection, $docComment);
    }

    /**
     * @return string[]
     */
    public function types(null|ReflectionType $type): array
    {
        $types = [];

        if ($type instanceof ReflectionNamedType) {
            $types[] = $type->getName();
            if ($type->allowsNull() && $type->getName() !== 'null') {
                $types[] = 'null';
            }
        }

        if ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $unionType) {
                if (! $unionType instanceof ReflectionNamedType) {
                    continue;
                }

                $types[] = $unionType->getName();
            }
        }

        return $types;
    }

    /**
     * @throws Exception
     */
    public function resolve(string|object $object, ?LoggerInterface $logger = null): ObjectReflection
    {
        if (! is_object($object) && ! class_exists($object) && ! interface_exists($object)) {
            throw new InvalidArgumentException(sprintf('object %s does not exist', $object));
        }

        $constructor = [];
        $properties = [];
        $setters = [];

        $reflectionClass = new ReflectionClass($object);

        /**
         * @todo implement UseStatementsResolver.php
         */
        $useStatementsReflection = new UseStatementsReflection([
            new UseStatementReflection(
                \DataMapper\Tests\MockClasses\Sub\SubItemConstructor::class,
                'SubItemConstructor',
            ),
        ]);
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $properties[] = new PropertyReflection(
                $this->name($reflectionProperty),
                $this->types($reflectionProperty->getType()),
                $this->annotation($useStatementsReflection, $reflectionProperty),
            );
        }

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            if (str_starts_with($reflectionMethod->getName(), '__construct')) {
                foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
                    $constructor[] = new PropertyReflection(
                        $this->name($reflectionParameter),
                        $this->types($reflectionParameter->getType()),
                        $this->annotation($useStatementsReflection, $reflectionMethod),
                    );
                }
            }

            if (str_starts_with($reflectionMethod->getName(), 'set')) {
                if (count($reflectionMethod->getParameters()) !== 1) {
                    if ($logger instanceof LoggerInterface) {
                        $logger->warning(
                            sprintf(
                                'Method %s has more than one parameter',
                                $reflectionMethod->getName(),
                            ),
                        );
                    }

                    continue;
                }

                $setters[] = new PropertyReflection(
                    $this->name($reflectionMethod),
                    $this->types($reflectionMethod->getParameters()[0]->getType()),
                    $this->annotation($useStatementsReflection, $reflectionMethod),
                );
            }
        }

        return new ObjectReflection(
            $constructor,
            $properties,
            $setters,
        );
    }
}
