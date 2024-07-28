<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Reflection;

use Wundii\DataMapper\Enum\DataTypeEnum;

/**
 * @todo performance optimization ~3 tenths possible with 10000 repeats
 */

final readonly class PropertyReflection
{
    /**
     * @param string[] $types
     */
    public function __construct(
        private string $name,
        private array $types,
        private AnnotationReflection $annotationReflection,
    ) {
    }

    public function getDataType(): string|DataTypeEnum
    {
        if (! $this->isOneType()) {
            return DataTypeEnum::NULL;
        }

        foreach ($this->getTypes() as $type) {
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

    public function getTargetType(bool $classOnly = false): ?string
    {
        $lowestType = null;

        foreach ($this->getTypes() as $type) {
            if (class_exists($type) || interface_exists($type)) {
                if ($classOnly) {
                    return $type;
                }

                $lowestType = $type;
            }

            if (str_ends_with($type, '[]')) {
                $classType = substr($type, 0, -2);
                if (class_exists($classType) || interface_exists($classType)) {
                    if ($classOnly) {
                        return $classType;
                    }

                    $lowestType = 'array';
                }

                $classType = DataTypeEnum::fromString($classType);
                if ($classType instanceof DataTypeEnum) {
                    $lowestType = $classType->value;
                }
            }

            if ($lowestType === null && ! $classOnly) {
                $lowestType = $type;
            }
        }

        return $lowestType;
    }

    public function isOneType(): bool
    {
        if (count($this->getTypes()) === 1) {
            return true;
        }

        $types = [];
        $isArray = false;
        foreach ($this->getTypes() as $type) {
            if (strtolower($type) === 'null') {
                continue;
            }

            if (str_ends_with($type, '[]')) {
                $isArray = true;
            }

            $types[] = $type;
        }

        if ($isArray) {
            $types = array_filter($types, fn (string $type): bool => strtolower($type) === 'array');
        }

        return count($types) === 1;
    }

    public function isNullable(): bool
    {
        $types = array_map(fn (string $type): string => strtolower($type), $this->types);

        return in_array('null', $types, true);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        $types = $this->types;

        $types = array_merge($types, $this->annotationReflection->getVariables());

        foreach ($this->annotationReflection->getParameterReflections() as $parameterReflection) {
            $name = $this->name;

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

    public function getAnnotation(): AnnotationReflection
    {
        return $this->annotationReflection;
    }
}
