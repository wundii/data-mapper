<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use ReflectionClass;
use ReflectionException;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Reflection\AnnotationReflection;
use Wundii\DataMapper\Reflection\ObjectReflection;
use Wundii\DataMapper\Reflection\ParameterReflection;
use Wundii\DataMapper\Reflection\PropertyReflection;
use Wundii\DataMapper\Reflection\UseStatementsReflection;

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
            if (class_exists($type)) {
                continue;
            }

            $classString = $useStatementsReflection->find($type);

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
     * @throws DataMapperException|ReflectionException
     */
    public function resolve(string|object $object): ObjectReflection
    {
        if (! is_object($object) && interface_exists($object)) {
            throw DataMapperException::InvalidArgument(sprintf('%s: interfaces are not allowed', $object));
        }

        if (! is_object($object) && ! class_exists($object)) {
            throw DataMapperException::InvalidArgument(sprintf('object %s does not exist', $object));
        }

        $constructor = [];
        $properties = [];
        $setters = [];

        $reflectionClass = new ReflectionClass($object);
        $useStatementsReflection = (new ReflectionTokenResolver())->resolve($object);

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            if (str_starts_with($reflectionMethod->getName(), '__construct')) {
                foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
                    $constructor[] = (new PropertyReflectionResolver())->resolve(
                        $this->name($reflectionParameter),
                        $this->types($reflectionParameter->getType()),
                        $this->annotation($useStatementsReflection, $reflectionMethod),
                        $object,
                    );
                }
            }

            if (str_starts_with($reflectionMethod->getName(), 'set')) {
                if (count($reflectionMethod->getParameters()) !== 1) {
                    continue;
                }

                $setters[] = (new PropertyReflectionResolver())->resolve(
                    $this->name($reflectionMethod),
                    $this->types($reflectionMethod->getParameters()[0]->getType()),
                    $this->annotation($useStatementsReflection, $reflectionMethod),
                    $object,
                );
            }
        }

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $annotation = $this->annotation($useStatementsReflection, $reflectionProperty);
            $propertyReflection = null;

            if ($annotation->isEmpty()) {
                foreach ($constructor as $property) {
                    if ($property->getName() !== $this->name($reflectionProperty)) {
                        continue;
                    }

                    $propertyReflection = $property;
                    break;
                }
            }

            if (! $propertyReflection instanceof PropertyReflection) {
                $propertyReflection = (new PropertyReflectionResolver())->resolve(
                    $this->name($reflectionProperty),
                    $this->types($reflectionProperty->getType()),
                    $annotation,
                    $object,
                );
            }

            $properties[] = $propertyReflection;
        }

        return new ObjectReflection(
            $properties,
            $constructor,
            $setters,
        );
    }
}
