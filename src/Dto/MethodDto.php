<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Dto;

use Wundii\DataMapper\Attribute\SourceData;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\DataTypeEnum;
use Wundii\DataMapper\Enum\MethodTypeEnum;
use Wundii\DataMapper\Interface\ElementDtoInterface;

final readonly class MethodDto implements ElementDtoInterface
{
    /**
     * @param string[] $returnTypes
     * @param ParameterDto[] $parameters
     * @param AttributeDto[] $attributes
     */
    public function __construct(
        private MethodTypeEnum $methodTypeEnum,
        private AccessibleEnum $accessibleEnum,
        private string $name,
        private string|DataTypeEnum $dataType,
        private ?string $targetType,
        private bool $nullable,
        private mixed $value = null,
        private array $returnTypes = [],
        private ?AnnotationDto $annotationDto = null,
        private array $parameters = [],
        private array $attributes = [],
    ) {
    }

    public function getMethodTypeEnum(): MethodTypeEnum
    {
        return $this->methodTypeEnum;
    }

    public function getAccessibleEnum(): AccessibleEnum
    {
        return $this->accessibleEnum;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getterName(): string
    {
        $sourceName = $this->name;

        foreach ($this->attributes as $attribute) {
            if ($attribute->getClassString() !== SourceData::class) {
                continue;
            }

            if (! is_string($attribute->getArguments()['target'])) {
                continue;
            }

            $sourceName = $attribute->getArguments()['target'];
        }

        if ($this->methodTypeEnum === MethodTypeEnum::GETTER) {
            $name = strtolower($sourceName);
            $removePrefix = [
                'get',
                'is',
                'has',
            ];

            foreach ($removePrefix as $prefix) {
                if (str_starts_with($name, $prefix)) {
                    return substr($sourceName, strlen($prefix));
                }
            }
        }

        return $sourceName;
    }

    public function getGetterName(): string
    {
        return $this->name;
    }

    public function getDataType(): string|DataTypeEnum
    {
        return $this->dataType;
    }

    public function getTargetType(): ?string
    {
        return $this->targetType;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @return string[]
     */
    public function getReturnTypes(): array
    {
        return $this->returnTypes;
    }

    public function getAnnotationDto(): ?AnnotationDto
    {
        return $this->annotationDto;
    }

    /**
     * @return ParameterDto[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return AttributeDto[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getStringValue(): string
    {
        if (
            $this->value === null
            || is_int($this->value)
            || is_float($this->value)
            || is_numeric($this->value)
            || is_bool($this->value)
            || is_string($this->value)
        ) {
            return (string) $this->value;
        }

        return '';
    }
}
