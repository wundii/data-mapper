<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;
use Wundii\DataMapper\Dto\AnnotationDto;
use Wundii\DataMapper\Dto\AttributeDto;
use Wundii\DataMapper\Dto\ReflectionObjectDto;
use Wundii\DataMapper\Dto\UseStatementsDto;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\AttributeInterface;

class ReflectionClassResolver extends AbstractReflectionClassResolver
{
    private PropertyDtoResolver $propertyDtoResolver;

    public function __construct(
        private UseStatementsDto $useStatementsDto,
    ) {
        $this->propertyDtoResolver = new PropertyDtoResolver();
    }

    public function resolvePropertiesConst(ReflectionMethod $reflectionMethod, object|string $objectOrClass, bool $takeValue): array
    {
        $propertiesConst = [];

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $propertiesConst[$reflectionParameter->getName()] = $this->propertyDtoResolver->resolve(
                $reflectionMethod,
                $reflectionParameter,
                $objectOrClass,
                $takeValue,
            );
        }

        return $propertiesConst;
    }

    public function resolve(object|string $objectOrClass, bool $takeValue = false): ReflectionObjectDto
    {
        if (! is_object($objectOrClass) && interface_exists($objectOrClass)) {
            throw DataMapperException::InvalidArgument(sprintf('%s: interfaces are not allowed', $objectOrClass));
        }

        if (! is_object($objectOrClass) && ! class_exists($objectOrClass)) {
            throw DataMapperException::InvalidArgument(sprintf('object %s does not exist', $objectOrClass));
        }

        $reflectionClass = $this->getReflectionClass($objectOrClass);

        $attributesClass = [];
        $propertiesClass = [];
        $propertiesConst = [];
        $methodsGetClass = [];
        $methodsOthClass = [];
        $methodsSetClass = [];

        foreach ($reflectionClass->getAttributes() as $attribute) {
            $instance = $attribute->newInstance();
            if (! $instance instanceof AttributeInterface) {
                continue;
            }

            /**
             * ausgabe testen ob dies in AttributePropertyDto übertragen werden kann
             */
            $attribute->getArguments();

            $attributesClass[] = new AttributeDto(
                $attribute->getName(),
                []
            );
        }

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $methodName = strtolower($reflectionMethod->getName());

            if (str_starts_with($methodName, '__construct')) {
                $propertiesConst = $this->resolvePropertiesConst(
                    $reflectionMethod,
                    $objectOrClass,
                    $takeValue,
                );
            }
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
}