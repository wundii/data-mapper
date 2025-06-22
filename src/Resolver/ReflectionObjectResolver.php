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
use Wundii\DataMapper\Dto\AnnotationDto;
use Wundii\DataMapper\Dto\ObjectPropertyDto;
use Wundii\DataMapper\Dto\ParameterDto;
use Wundii\DataMapper\Dto\PropertyDto;
use Wundii\DataMapper\Dto\UseStatementsDto;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\AttributeInterface;

final readonly class ReflectionObjectResolver
{
    public function name(ReflectionProperty|ReflectionParameter|ReflectionMethod $reflection): string
    {
        return $reflection->getName();
    }

    public function accessible(ReflectionProperty|ReflectionMethod $reflection): AccessibleEnum
    {
        if ($reflection->isPublic()) {
            return AccessibleEnum::PUBLIC;
        }

        if ($reflection->isProtected()) {
            return AccessibleEnum::PROTECTED;
        }

        return AccessibleEnum::PRIVATE;
    }

    public function parseAnnotation(UseStatementsDto $useStatementsDto, string $docComment): AnnotationDto
    {
        $parameterReflections = [];
        $variables = [];
        $docComment = trim($docComment);

        if (! str_starts_with($docComment, '/**')) {
            return new AnnotationDto([], []);
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

                $parameterTypes = $this->completeClassStrings($useStatementsDto, $parameterTypes);

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

                $variables = $this->completeClassStrings($useStatementsDto, $variables);
            }
        }

        return new AnnotationDto(
            $parameterReflections,
            $variables,
        );
    }

    /**
     * @param string[] $types
     * @return string[]
     */
    public function completeClassStrings(UseStatementsDto $useStatementsDto, array $types): array
    {
        foreach ($types as $key => $type) {
            if (class_exists($type)) {
                continue;
            }

            $classString = $useStatementsDto->findClassString($type);

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

            $classString = $useStatementsDto->findClassString($classString);

            if ($classString !== null) {
                $types[$key] = $classString . '[]';
            }
        }

        return $types;
    }

    public function annotationDto(UseStatementsDto $useStatementsDto, ReflectionProperty|ReflectionFunctionAbstract $property): AnnotationDto
    {
        $docComment = $property->getDocComment();

        if ($docComment === false) {
            return new AnnotationDto([], []);
        }

        return $this->parseAnnotation($useStatementsDto, $docComment);
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
    public function resolve(string|object $object, bool $takeValue = false): ObjectPropertyDto
    {
        if (! is_object($object) && interface_exists($object)) {
            throw DataMapperException::InvalidArgument(sprintf('%s: interfaces are not allowed', $object));
        }

        if (! is_object($object) && ! class_exists($object)) {
            throw DataMapperException::InvalidArgument(sprintf('object %s does not exist', $object));
        }

        $invokeObject = null;
        if ($takeValue && is_object($object)) {
            $invokeObject = $object;
        }

        $constructor = [];
        $properties = [];
        $getters = [];
        $setters = [];
        $attributes = [];

        $reflectionClass = new ReflectionClass($object);
        $useStatementsDto = (new ReflectionTokenResolver())->resolve($object);

        foreach ($reflectionClass->getAttributes() as $attribute) {
            $instance = $attribute->newInstance();
            if (! $instance instanceof AttributeInterface) {
                continue;
            }

            $attributes[] = (new PropertyDtoResolver())->resolve(
                $instance->getName() ?? $reflectionClass->getName(),
                [],
                new AnnotationDto([], []),
                $object,
                AccessibleEnum::PUBLIC,
                $instance->getValue(),
                $attribute->getName(),
            );
        }

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $methodName = strtolower($reflectionMethod->getName());
            $annotationDto = $this->annotationDto($useStatementsDto, $reflectionMethod);

            if (str_starts_with($methodName, '__construct')) {
                foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
                    $constructor[] = (new PropertyDtoResolver())->resolve(
                        $this->name($reflectionParameter),
                        $this->types($reflectionParameter->getType()),
                        $annotationDto,
                        $object,
                        $this->accessible($reflectionMethod),
                    );
                }
            }

            foreach ($reflectionMethod->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();
                if (! $instance instanceof AttributeInterface) {
                    continue;
                }

                $attributes[] = (new PropertyDtoResolver())->resolve(
                    $instance->getName() ?? $this->name($reflectionMethod),
                    $this->types($reflectionMethod->getReturnType()),
                    $annotationDto,
                    $object,
                    $this->accessible($reflectionMethod),
                    $takeValue ? ($instance->getValue() ?: $reflectionMethod->invoke($invokeObject)) : $instance->getValue(),
                    $attribute->getName(),
                );
            }

            if (str_starts_with($methodName, 'get')) {
                if ($reflectionMethod->getParameters() !== []) {
                    continue;
                }

                $getters[] = (new PropertyDtoResolver())->resolve(
                    substr($methodName, 3),
                    $this->types($reflectionMethod->getReturnType()),
                    $annotationDto,
                    $object,
                    $this->accessible($reflectionMethod),
                    $takeValue ? $reflectionMethod->invoke($invokeObject) : null,
                );
            }

            if (str_starts_with($methodName, 'is')) {
                if ($reflectionMethod->getParameters() !== []) {
                    continue;
                }

                $getters[] = (new PropertyDtoResolver())->resolve(
                    substr($methodName, 2),
                    $this->types($reflectionMethod->getReturnType()),
                    $annotationDto,
                    $object,
                    $this->accessible($reflectionMethod),
                    $takeValue ? $reflectionMethod->invoke($invokeObject) : null,
                );
            }

            if (str_starts_with($methodName, 'set')) {
                if (count($reflectionMethod->getParameters()) !== 1) {
                    continue;
                }

                $setters[] = (new PropertyDtoResolver())->resolve(
                    $this->name($reflectionMethod),
                    $this->types($reflectionMethod->getParameters()[0]->getType()),
                    $annotationDto,
                    $object,
                    $this->accessible($reflectionMethod),
                );
            }
        }

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $annotationDto = $this->annotationDto($useStatementsDto, $reflectionProperty);
            $propertyReflection = null;

            if ($annotationDto->isEmpty() && ! $takeValue) {
                foreach ($constructor as $property) {
                    if ($property->getName() !== $this->name($reflectionProperty)) {
                        continue;
                    }

                    $propertyReflection = $property;
                    break;
                }
            }

            foreach ($reflectionProperty->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();
                if (! $instance instanceof AttributeInterface) {
                    continue;
                }

                $attributes[] = (new PropertyDtoResolver())->resolve(
                    $instance->getName() ?? $this->name($reflectionProperty),
                    $this->types($reflectionProperty->getType()),
                    $annotationDto,
                    $object,
                    $this->accessible($reflectionProperty),
                    $takeValue ? ($instance->getValue() ?: $reflectionProperty->getValue($invokeObject)) : $instance->getValue(),
                    $attribute->getName(),
                );
            }

            if (! $propertyReflection instanceof PropertyDto) {
                $propertyReflection = (new PropertyDtoResolver())->resolve(
                    $this->name($reflectionProperty),
                    $this->types($reflectionProperty->getType()),
                    $annotationDto,
                    $object,
                    $this->accessible($reflectionProperty),
                    $takeValue ? $reflectionProperty->getValue($invokeObject) : null,
                );
            }

            $properties[] = $propertyReflection;
        }

        return new ObjectPropertyDto(
            $properties,
            $constructor,
            $getters,
            $setters,
            $attributes,
        );
    }
}
