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
    ): array
    {
        $types = array_merge($types, $annotationReflection->getVariables());

        foreach ($annotationReflection->getParameterReflections() as $parameterReflection) {
            if (str_starts_with($name, 'set')) {
                $name = substr($name, 3);
            }

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
    public function getTargetType(array $types): ?string
    {
        $lowestType = null;

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
                    $lowestType = $classType->value;
                }
            }
        }

        return $lowestType;
    }

    public function getDataType(bool $oneType, array $targetTypes): string|DataTypeEnum
    {
        if ($oneType === false) {
            return DataTypeEnum::NULL;
        }

        foreach ($targetTypes as $type) {
            $type = DataTypeEnum::fromString($type);

            if ($type === DataTypeEnum::NULL) {
                continue;
            }

            if (is_string($type) && str_ends_with($type, '[]')) {
                continue;
            }

            if (is_string($type) && (class_exists($type) || interface_exists($type))) {
                return DataTypeEnum::OBJECT;
            }

            return $type;
        }

        return DataTypeEnum::NULL;
    }

    /**
     * @param string[] $types
     */
    public function isNullable(array $types): bool
    {
        $types = array_map(fn (string $type): string => strtolower($type), $types);

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

    public function resolve(
        string $name,
        array $types,
        AnnotationReflection $annotationReflection,
    ): PropertyReflection
    {
        $targetTypes = $this->getTargetTypes($name, $types, $annotationReflection);

        $oneType = $this->isOneType($targetTypes);
        $nullable = $this->isNullable($targetTypes);

        $targetType = $this->getTargetType($targetTypes);
        $dataType = $this->getDataType($oneType, $targetTypes);

        return new PropertyReflection(
            $name,
            $dataType,
            $targetType,
            $oneType,
            $nullable,
        );
    }
}
