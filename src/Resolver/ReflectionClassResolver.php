<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use Wundii\DataMapper\Dto\AttributeDto;
use Wundii\DataMapper\Dto\MethodDto;
use Wundii\DataMapper\Dto\PropertyDto;
use Wundii\DataMapper\Dto\ReflectionObjectDto;
use Wundii\DataMapper\Dto\UseStatementsDto;
use Wundii\DataMapper\Enum\AttributeOriginEnum;
use Wundii\DataMapper\Enum\MethodTypeEnum;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\AttributeInterface;

/**
 * @template T of object
 * @extends AbstractReflectionResolver<T>
 */
class ReflectionClassResolver extends AbstractReflectionResolver
{
    private ReflectionAnnotationResolver $reflectionAnnotationResolver;

    public function __construct(
        UseStatementsDto $useStatementsDto,
    ) {
        parent::__construct();

        $this->reflectionAnnotationResolver = new ReflectionAnnotationResolver($useStatementsDto);
    }

    /**
     * @param class-string<T>|T $objectOrClass
     * @throws ReflectionException
     */
    public function resolve(object|string $objectOrClass, bool $takeValue = false): ReflectionObjectDto
    {
        if (! is_object($objectOrClass) && interface_exists($objectOrClass)) {
            throw DataMapperException::InvalidArgument(sprintf('%s: interfaces are not allowed', $objectOrClass));
        }

        if (! is_object($objectOrClass) && ! class_exists($objectOrClass)) {
            throw DataMapperException::InvalidArgument(sprintf('object %s does not exist', $objectOrClass));
        }

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
                $propertiesClass,
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
     * @param ReflectionClass<T>|ReflectionMethod|ReflectionProperty $reflection
     * @return AttributeDto[]
     */
    private function resolveAttributes(
        ReflectionClass|ReflectionMethod|ReflectionProperty $reflection,
        AttributeOriginEnum $attributeOriginEnum,
    ): array {
        $attributes = [];

        foreach ($reflection->getAttributes() as $attribute) {
            $instance = $attribute->newInstance();
            if (! $instance instanceof AttributeInterface) {
                continue;
            }

            $arguments = [];
            /** @phpstan-ignore-next-line */
            $classProperties = $this->reflectionClassPropertiesCache($instance::class);

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

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $annotation = $this->reflectionAnnotationResolver->resolve($reflectionProperty->getDocComment());
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
                $elementDto->getName(),
                $elementDto->getDataType(),
                $elementDto->getTargetType(),
                $elementDto->isNullable(),
                $elementDto->getAccessibleEnum(),
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
        $returnType = $reflectionMethod->getReturnType() ?? null;
        $returnType = match (true) {
            $returnType instanceof ReflectionNamedType => $returnType->getName(),
            $returnType instanceof ReflectionUnionType => implode('|', $returnType->getTypes()),
            default => null,
        };

        $methodTypEnum = match (true) {
            $returnType === 'void' => MethodTypeEnum::SETTER,
            is_string($returnType) && $returnType !== 'void' => MethodTypeEnum::GETTER,
            default => MethodTypeEnum::OTHER,
        };

        $attributes = $this->resolveAttributes($reflectionMethod, AttributeOriginEnum::TARGET_METHOD);
        $annotation = $this->reflectionAnnotationResolver->resolve($reflectionMethod->getDocComment());

        $value = $takeValue && is_object($objectOrClass)
            ? $reflectionMethod->invoke($objectOrClass)
            : null;

        return new MethodDto(
            $methodTypEnum,
            ReflectionElementResolver::accessible($reflectionMethod),
            $reflectionMethod->getName(),
            $value,
            ReflectionElementResolver::types($reflectionMethod->getReturnType()),
            $annotation,
            [],
            $attributes,
        );
    }

    /**
     * @param class-string<T>|T $objectOrClass
     * @param PropertyDto[] $propertiesClass
     * @return PropertyDto[]
     * @throws ReflectionException
     */
    private function resolvePropertiesConst(
        ReflectionMethod $reflectionMethod,
        object|string $objectOrClass,
        array $propertiesClass,
    ): array {
        if (strcasecmp($reflectionMethod->getName(), '__construct') !== 0) {
            return [];
        }

        $properties = [];

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            if (array_key_exists($reflectionParameter->getName(), $propertiesClass)) {
                $properties[$reflectionParameter->getName()] = $propertiesClass[$reflectionParameter->getName()];
                continue;
            }

            $annotation = $this->reflectionAnnotationResolver->resolve($reflectionMethod->getDocComment());
            $elementDto = $this->reflectionElementsCache(
                $objectOrClass,
                $reflectionMethod,
                $reflectionParameter,
                $annotation,
            );

            $properties[$reflectionParameter->getName()] = new PropertyDto(
                $elementDto->getName(),
                $elementDto->getDataType(),
                $elementDto->getTargetType(),
                $elementDto->isNullable(),
                $elementDto->getAccessibleEnum(),
                $elementDto->isDefaultValueAvailable(),
                $elementDto->getDefaultValue(),
                null,
                $annotation,
            );
        }

        return $properties;
    }
}
