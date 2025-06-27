<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;
use Wundii\DataMapper\Dto\AnnotationDto;
use Wundii\DataMapper\Dto\AttributeDto;
use Wundii\DataMapper\Dto\ElementDto;
use Wundii\DataMapper\Dto\MethodDto;
use Wundii\DataMapper\Dto\ParameterDto;
use Wundii\DataMapper\Dto\PropertyDto;
use Wundii\DataMapper\Dto\ReflectionObjectDto;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\AttributeOriginEnum;
use Wundii\DataMapper\Enum\MethodTypeEnum;
use Wundii\DataMapper\Exception\DataMapperException;

/**
 * @template T of object
 * @extends AbstractReflectionResolver<T>
 */
class ReflectionClassResolver extends AbstractReflectionResolver
{
    private ReflectionAnnotationResolver $reflectionAnnotationResolver;

    /**
     * @param class-string<T>|T $objectOrClass
     * @throws ReflectionException
     * @throws DataMapperException
     */
    public function resolve(object|string $objectOrClass, bool $takeValue = false): ReflectionObjectDto
    {
        if (! is_object($objectOrClass) && interface_exists($objectOrClass)) {
            throw DataMapperException::InvalidArgument(sprintf('%s: interfaces are not allowed', $objectOrClass));
        }

        if (! is_object($objectOrClass) && ! class_exists($objectOrClass)) {
            throw DataMapperException::InvalidArgument(sprintf('object %s does not exist', $objectOrClass));
        }

        $useStatementsDto = (new ReflectionUseResolver())->resolve($objectOrClass);
        $this->reflectionAnnotationResolver = new ReflectionAnnotationResolver($useStatementsDto);

        $reflectionClass = $this->reflectionClassCache($objectOrClass);
        $attributesClass = $this->resolveAttributes($reflectionClass, AttributeOriginEnum::TARGET_CLASS);
        $propertiesClass = $this->resolvePropertiesClass($reflectionClass, $objectOrClass, $takeValue);
        $propertiesConst = [];
        $methodsGetClass = [];
        $methodsOthClass = [];
        $methodsSetClass = [];

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $method = $this->resolveMethods(
                $reflectionMethod,
                $objectOrClass,
                $takeValue,
            );

            switch ($method->getMethodTypeEnum()) {
                case MethodTypeEnum::GETTER:
                    $methodsGetClass[] = $method;
                    break;
                case MethodTypeEnum::SETTER:
                    $methodsSetClass[] = $method;
                    break;
                default:
                    $methodsOthClass[] = $method;
                    break;
            }

            $propertiesConst += $this->resolvePropertiesConst(
                $reflectionMethod,
                $objectOrClass,
                $takeValue,
            );
        }

        return new ReflectionObjectDto(
            $attributesClass,
            $propertiesClass,
            $propertiesConst,
            $methodsGetClass,
            $methodsOthClass,
            $methodsSetClass,
        );
    }

    /**
     * @param ReflectionClass<T>|ReflectionMethod|ReflectionProperty|ReflectionParameter $reflection
     * @return AttributeDto[]
     */
    private function resolveAttributes(
        ReflectionClass|ReflectionMethod|ReflectionProperty|ReflectionParameter $reflection,
        AttributeOriginEnum $attributeOriginEnum,
    ): array {
        $attributes = [];

        foreach ($reflection->getAttributes() as $attribute) {
            $arguments = [];
            /** @phpstan-ignore-next-line */
            $classProperties = $this->reflectionClassPropertiesCache($attribute->getName());

            foreach ($attribute->getArguments() as $property => $argument) {
                if (is_int($property)) {
                    $property = $classProperties[$property] ?? $property;
                }

                $arguments[$property] = $argument;
            }

            $attributes[] = new AttributeDto(
                $attributeOriginEnum,
                $reflection->getName(),
                $attribute->getName(),
                $arguments,
            );
        }

        return $attributes;
    }

    /**
     * @param ReflectionClass<T> $reflectionClass
     * @param class-string<T>|T $objectOrClass
     * @return PropertyDto[]
     * @throws ReflectionException
     */
    private function resolvePropertiesClass(
        ReflectionClass $reflectionClass,
        object|string $objectOrClass,
        bool $takeValue,
    ): array {
        $properties = [];
        $invokeObject = $takeValue && is_object($objectOrClass) ? $objectOrClass : null;

        $constructorAnnotation = null;
        if ($reflectionClass->hasMethod('__construct')) {
            $constructorMethod = $reflectionClass->getMethod('__construct');
            $constructorAnnotation = $this->reflectionAnnotationResolver->resolve($constructorMethod->getDocComment());
        }

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->isPromoted()) {
                // Skip promoted properties, as they are already handled in the constructor
                continue;
            }

            $annotation = $this->reflectionAnnotationResolver->resolve($reflectionProperty->getDocComment());
            if (! $annotation instanceof AnnotationDto && $constructorAnnotation instanceof AnnotationDto) {
                $annotation = $constructorAnnotation;
            }

            $attributes = $this->resolveAttributes($reflectionProperty, AttributeOriginEnum::TARGET_PROPERTY);
            $elementDto = $this->reflectionElementsCache(
                $objectOrClass,
                $reflectionProperty,
                $reflectionProperty,
                $annotation,
            );

            $value = $takeValue && is_object($objectOrClass)
                ? $reflectionProperty->getValue($invokeObject)
                : null;

            $properties[$reflectionProperty->getName()] = new PropertyDto(
                $elementDto->getAccessibleEnum(),
                $elementDto->getName(),
                $elementDto->getDataType(),
                $elementDto->getTargetType(),
                $elementDto->isNullable(),
                $elementDto->isDefaultValueAvailable(),
                $elementDto->getDefaultValue(),
                $value,
                $annotation,
                $attributes,
            );
        }

        return $properties;
    }

    /**
     * @param class-string<T>|T $objectOrClass
     * @throws ReflectionException
     */
    private function resolveMethods(
        ReflectionMethod $reflectionMethod,
        object|string $objectOrClass,
        bool $takeValue,
    ): MethodDto {
        $classString = is_object($objectOrClass) ? get_class($objectOrClass) : $objectOrClass;
        $returnType = $reflectionMethod->getReturnType() ?? null;
        $returnType = match (true) {
            $returnType instanceof ReflectionNamedType => $returnType->getName(),
            $returnType instanceof ReflectionUnionType => implode('|', $returnType->getTypes()),
            default => null,
        };

        $returnType = is_string($returnType) ? strtolower($returnType) : $returnType;

        $methodTypEnum = match (true) {
            $returnType === 'void', $returnType === 'self', $returnType === $classString => MethodTypeEnum::SETTER,
            is_string($returnType) && $returnType !== 'void' => MethodTypeEnum::GETTER,
            default => MethodTypeEnum::OTHER,
        };

        $attributes = $this->resolveAttributes($reflectionMethod, AttributeOriginEnum::TARGET_METHOD);
        $annotation = $this->reflectionAnnotationResolver->resolve($reflectionMethod->getDocComment());

        $firstElementDto = null;
        $parameters = [];
        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $elementDto = $this->reflectionElementsCache(
                $objectOrClass,
                $reflectionMethod,
                $reflectionParameter,
                $annotation,
            );

            $firstElementDto = $firstElementDto instanceof ElementDto
                ? $firstElementDto
                : $elementDto;

            $parameters[] = new ParameterDto(
                $elementDto->getName(),
                $elementDto->getTypes(),
                $elementDto->isDefaultValueAvailable(),
                $elementDto->getDefaultValue(),
            );
        }

        if ($methodTypEnum === MethodTypeEnum::SETTER && count($parameters) !== 1) {
            $methodTypEnum = MethodTypeEnum::OTHER;
        }

        if ($methodTypEnum === MethodTypeEnum::GETTER && $parameters !== []) {
            $methodTypEnum = MethodTypeEnum::OTHER;
        }

        $value = null;
        if (
            $takeValue
            && is_object($objectOrClass)
            && strcasecmp($reflectionMethod->getName(), '__construct') !== 0
            && $methodTypEnum === MethodTypeEnum::GETTER
        ) {
            $value = $reflectionMethod->invoke($objectOrClass);
        }

        return new MethodDto(
            $methodTypEnum,
            ReflectionElementResolver::accessible($reflectionMethod),
            $reflectionMethod->getName(),
            $firstElementDto?->getDataType() ?? '',
            $firstElementDto?->getTargetType(),
            $firstElementDto?->isNullable() ?? false,
            $value,
            $firstElementDto?->getTypes() ?? [],
            $annotation,
            $parameters,
            $attributes,
        );
    }

    /**
     * @param class-string<T>|T $objectOrClass
     * @return PropertyDto[]
     * @throws ReflectionException
     */
    private function resolvePropertiesConst(
        ReflectionMethod $reflectionMethod,
        object|string $objectOrClass,
        bool $takeValue,
    ): array {
        if (strcasecmp($reflectionMethod->getName(), '__construct') !== 0) {
            return [];
        }

        $properties = [];

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $annotation = $this->reflectionAnnotationResolver->resolve($reflectionMethod->getDocComment());
            $attributes = $this->resolveAttributes($reflectionParameter, AttributeOriginEnum::TARGET_PROPERTY);
            $elementDto = $this->reflectionElementsCache(
                $objectOrClass,
                $reflectionMethod,
                $reflectionParameter,
                $annotation,
            );

            $value = null;
            if (
                $takeValue
                && $elementDto->getAccessibleEnum() === AccessibleEnum::PUBLIC
                && is_object($objectOrClass)
            ) {
                $reflectionClass = $reflectionParameter->getDeclaringClass();
                if ($reflectionClass instanceof ReflectionClass) {
                    $reflectionProperty = $reflectionClass->getProperty($reflectionParameter->getName());
                    $value = $reflectionProperty->getValue($objectOrClass);
                }
            }

            $properties[$reflectionParameter->getName()] = new PropertyDto(
                $elementDto->getAccessibleEnum(),
                $elementDto->getName(),
                $elementDto->getDataType(),
                $elementDto->getTargetType(),
                $elementDto->isNullable(),
                $elementDto->isDefaultValueAvailable(),
                $elementDto->getDefaultValue(),
                $value,
                $annotation,
                $attributes,
            );
        }

        return $properties;
    }
}
