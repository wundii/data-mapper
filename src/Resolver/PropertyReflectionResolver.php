<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use Wundii\DataMapper\Enum\DataTypeEnum;
use Wundii\DataMapper\Reflection\AnnotationReflection;
use Wundii\DataMapper\Reflection\PropertyReflection;

final readonly class PropertyReflectionResolver
{
    /**
     * @param string[] $types
     * @return string[]
     */
    public function getTargetTypes(
        string $name,
        array $types,
        AnnotationReflection $annotationReflection,
    ): array {
        if (str_starts_with($name, 'set')) {
            $name = substr($name, 3);
        }

        $types = array_merge($types, $annotationReflection->getVariables());

        foreach ($annotationReflection->getParameterReflections() as $parameterReflection) {
            if (strcasecmp($parameterReflection->getParameter(), $name) === 0) {
                $types = array_merge($types, $parameterReflection->getTypes());
                break;
            }
        }

        return array_values(array_unique($types));
    }

    /**
     * @param string[] $types
     */
    public function getTargetType(array $types, string|object $object): ?string
    {
        foreach ($types as $type) {
            if (class_exists($type) || interface_exists($type)) {
                return $type;
            }

            if (str_ends_with($type, '[]')) {
                $classType = substr($type, 0, -2);
                if (class_exists($classType) || interface_exists($classType)) {
                    return $classType;
                }

                $classType = DataTypeEnum::fromString($classType);
                if ($classType instanceof DataTypeEnum) {
                    return $classType->value;
                }
            }

            if (strtolower($type) === 'self') {
                if (is_object($object)) {
                    return get_class($object);
                }

                return $object;
            }
        }

        return null;
    }

    /**
     * @param string[] $targetTypes
     */
    public function getDataType(bool $oneType, array $targetTypes): string|DataTypeEnum
    {
        if (! $oneType) {
            return DataTypeEnum::NULL;
        }

        foreach ($targetTypes as $targetType) {
            if (strtolower($targetType) === 'self') {
                return DataTypeEnum::OBJECT;
            }

            $targetType = DataTypeEnum::fromString($targetType);

            if ($targetType === DataTypeEnum::NULL) {
                continue;
            }

            if (is_string($targetType) && str_ends_with($targetType, '[]')) {
                continue;
            }

            if (is_string($targetType) && (class_exists($targetType) || interface_exists($targetType))) {
                return DataTypeEnum::OBJECT;
            }

            return $targetType;
        }

        return DataTypeEnum::NULL;
    }

    /**
     * @param string[] $types
     */
    public function isNullable(array $types): bool
    {
        $types = array_map(static fn (string $type): string => strtolower($type), $types);

        return in_array('null', $types, true);
    }

    /**
     * @param string[] $types
     */
    public function isOneType(array $types): bool
    {
        if (count($types) === 1) {
            return true;
        }

        $tmp = [];
        $isArray = false;
        foreach ($types as $type) {
            if (strtolower($type) === 'null') {
                continue;
            }

            if (str_ends_with($type, '[]')) {
                $isArray = true;
            }

            $tmp[] = $type;
        }

        if ($isArray) {
            $tmp = array_filter($tmp, fn (string $type): bool => strtolower($type) === 'array');
        }

        return count($tmp) === 1;
    }

    /**
     * @param string[] $types
     */
    public function resolve(
        string $name,
        array $types,
        AnnotationReflection $annotationReflection,
        string|object $object,
    ): PropertyReflection {
        $targetTypes = $this->getTargetTypes($name, $types, $annotationReflection);

        $oneType = $this->isOneType($targetTypes);
        $nullable = $this->isNullable($targetTypes);

        $dataType = $this->getDataType($oneType, $targetTypes);
        $targetType = $this->getTargetType($targetTypes, $object);

        return new PropertyReflection(
            $name,
            $dataType,
            $targetType,
            $oneType,
            $nullable,
        );
    }
}
