<?php

declare(strict_types=1);

namespace DataMapper\SourceData;

use DataMapper\DataConfig;
use DataMapper\Elements\DataArray;
use DataMapper\Elements\DataBool;
use DataMapper\Elements\DataFloat;
use DataMapper\Elements\DataInt;
use DataMapper\Elements\DataObject;
use DataMapper\Elements\DataString;
use DataMapper\Interface\ElementArrayInterface;
use DataMapper\Interface\ElementDataInterface;
use DataMapper\Interface\ElementObjectInterface;
use DataMapper\Reflection\PropertyReflection;
use DataMapper\Resolver\ElementObjectResolver;
use DataMapper\Resolver\ReflectionObjectResolver;
use Exception;
use SimpleXMLElement;

final class XmlSourceData extends AbstractSourceData
{
    // public static function elementValue(
    //     SimpleXMLElement $xmlElement,
    // ): ElementDataInterface {
    //     return new DataString('');
    // }

    /**
     * @throws Exception
     */
    public function elementArray(
        DataConfig $dataConfig,
        SimpleXMLElement $xmlElement,
        null|string $object,
    ): ElementArrayInterface {
        /**
         * @todo Implement array element
         */
        // $objectReflection = (new ReflectionObjectResolver())->resolve($object ?: '');
        // $dataList = [];
        //
        // foreach ($xmlElement->children() as $child) {
        //     $name = $child->getName();
        //
        //     dump($child->getName(), (string) $child);
        //     if ($object !== null) {
        //         $childReflection = $objectReflection->find($dataConfig->getApproach(), $name);
        //         if (! $childReflection instanceof PropertyReflection) {
        //             continue;
        //         }
        //
        //         dump($childReflection->getType());
        //     }
        //
        //     dump($object);
        //     dump($child->getName(), (string) $child);
        //
        //     $dataList[] = match ($child->getName()) {
        //         'int' => new DataInt((string) $child),
        //         'float' => new DataFloat((string) $child),
        //         'object' => $this->elementObject($dataConfig, $child, $object),
        //         'string' => new DataString((string) $child),
        //         default => throw new Exception('Invalid element'),
        //     };
        //
        // }

        return new DataArray(
            [
                new DataString('hello'),
                new DataString('world'),
            ],
        );
    }

    /**
     * @throws Exception
     */
    public function elementObject(
        DataConfig $dataConfig,
        SimpleXMLElement $xmlElement,
        null|string|object $object,
    ): ElementDataInterface {
        $objectReflection = (new ReflectionObjectResolver())->resolve($object ?: '');
        $dataList = [];

        foreach ($xmlElement->children() as $child) {
            $name = $child->getName();
            $value = (string) $child;

            $childReflection = $objectReflection->find($dataConfig->getApproach(), $name);
            if (! $childReflection instanceof PropertyReflection) {
                continue;
            }

            $dataList[] = match ($childReflection->getType()) {
                'int' => new DataInt($value, $name),
                'float' => new DataFloat($value, $name),
                'bool' => new DataBool($value, $name),
                'array' => $this->elementArray($dataConfig, $child, $childReflection->getTargetType()),
                'object' => $this->elementObject($dataConfig, $child, $childReflection->getTargetType(true)),
                default => new DataString($value, $name),
            };
        }

        return new DataObject($object ?: '', $dataList);
    }

    /**
     * @throws Exception
     */
    public function resolve(): object
    {
        try {
            $xmlElement = new SimpleXmlElement($this->source);
        } catch (Exception $exception) {
            throw new Exception('Invalid XML: ' . $exception->getMessage(), (int) $exception->getCode(), $exception);
        }

        $elementData = $this->elementObject($this->dataConfig, $xmlElement, $this->object);
        if (! $elementData instanceof ElementObjectInterface) {
            throw new Exception('Invalid ElementDataInterface');
        }

        return (new ElementObjectResolver())->resolve($this->dataConfig, $elementData);
    }
}
