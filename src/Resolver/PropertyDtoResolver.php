<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Resolver;

use Wundii\DataMapper\Dto\AnnotationDto;
use Wundii\DataMapper\Dto\PropertyDto;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ClassElementTypeEnum;
use Wundii\DataMapper\Enum\DataTypeEnum;

final readonly class PropertyDtoResolver
{
    /**
     * @param string[] $types
     * @return string[]
     */
    public function getTargetTypes(
        string $name,
        array $types,
        AnnotationDto $annotationDto,
    ): array {
        if (str_starts_with($name, 'set')) {
            $name = substr($name, 3);
        }

        $types = array_merge($types, $annotationDto->getVariables());

        foreach ($annotationDto->getParameterDto() as $parameterDto) {
            if (strcasecmp($parameterDto->getParameter(), $name) === 0) {
                $types = array_merge($types, $parameterDto->getTypes());
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
        ClassElementTypeEnum $classElementTypeEnum,
        string $name,
        array $types,
        AnnotationDto $annotationDto,
        string|object $object,
        AccessibleEnum $accessibleEnum,
        mixed $value = null,
    ): PropertyDto {
        $targetTypes = $this->getTargetTypes($name, $types, $annotationDto);

        $oneType = $this->isOneType($targetTypes);
        $nullable = $this->isNullable($targetTypes);

        $dataType = $this->getDataType($oneType, $targetTypes);
        $targetType = $this->getTargetType($targetTypes, $object);

        return new PropertyDto(
            $classElementTypeEnum,
            $name,
            $dataType,
            $targetType,
            $oneType,
            $nullable,
            $accessibleEnum,
            $value,
        );
    }
}
