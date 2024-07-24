<?php

declare(strict_types=1);

namespace DataMapper\Reflection;

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

    public function getType(): ?string
    {
        if (! $this->isOneType()) {
            return null;
        }

        $types = $this->getTypes();

        if (count($types) === 1) {
            return array_shift($types);
        }

        foreach ($this->getTypes() as $type) {
            if (strtolower($type) === 'null') {
                continue;
            }

            if (str_ends_with($type, '[]')) {
                continue;
            }

            if (class_exists($type)) {
                return 'object';
            }

            return $type;
        }

        return null;
    }

    public function getClassString(): ?string
    {
        foreach ($this->getTypes() as $type) {
            if (class_exists($type)) {
                return $type;
            }

            if (! str_ends_with($type, '[]')) {
                continue;
            }

            $type = substr($type, 0, -2);
            if (class_exists($type)) {
                return $type;
            }
        }

        return null;
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
            if (strcasecmp($parameterReflection->getParameter(), $this->name) === 0) {
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
