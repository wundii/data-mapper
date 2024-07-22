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
use DataMapper\Resolver\ElementObjectResolver;
use DataMapper\Resolver\ReflectionObjectResolver;
use DataMapper\Tests\MockClasses\ItemClassConstructor;
use Exception;
use SimpleXMLElement;

final class XmlSourceData extends AbstractSourceData
{
    // public static function elementValue(
    //     SimpleXMLElement $xmlElement,
    // ): ElementDataInterface {
    //     return new DataString('');
    // }

    public static function elementArray(
        DataConfig $dataConfig,
        SimpleXMLElement $xmlElement,
    ): ElementArrayInterface {
        return new DataArray([]);
    }

    /**
     * @throws Exception
     */
    public static function elementObject(
        DataConfig $dataConfig,
        SimpleXMLElement $xmlElement,
        string|object $object,
    ): ElementDataInterface {
        (new ReflectionObjectResolver())->resolve($object);

        // $constructor = [
        //     'name' => 'string',
        //     'item' => ItemClassConstructor::class,
        //     'id' => 'null|int',
        //     'data' => 'array',
        // ];
        //
        // $properties = [
        //     'name' => 'string',
        //     'item' => ItemClassConstructor::class,
        //     'id' => 'null|int',
        //     'data' => 'array',
        // ];
        //
        // $setters = [
        //     'setName' => 'string',
        //     'setItem' => ItemClassConstructor::class,
        //     'setId' => 'null|int',
        //     'setData' => 'array',
        // ];

        // $value = [];
        //
        // foreach ($xmlElement->children() as $child) {
        //     $name = $child->getName();
        //     dump($name, $child->count());
        //     $type = null;
        //
        //     if (
        //         $dataObjectProperty instanceof DataObjectProperty
        //         && !in_array($name, $dataObjectProperty->getProperties(), true)
        //     ) {
        //         continue;
        //     }
        //
        //     if ($child->count() > 0) {
        //         $value[] = self::convertToElementData($child, $object);
        //     } else {
        //         $childValue = (string) $child;
        //
        //         $value[] = match ($type) {
        //             'int' => new DataInt((int) $childValue, $name),
        //             'float' => new DataFloat((float) $childValue, $name),
        //             'bool' => new DataBool((bool) $childValue, $name),
        //             default => new DataString($childValue, $name),
        //         };
        //     }
        // }
        //
        //
        // if ($object === null) {
        //     // return new DataArray($value, 'items');
        //     // return new DataBool(true, 'isAvailable');
        //     // return new DataFloat(12.34, 'price');
        //     // return new DataInt(1, 'id');
        //     return new DataString('constructor', 'name');
        // }

        $value = [
            new DataString('constructor', 'name'),
            new DataObject(
                ItemClassConstructor::class,
                [
                    new DataFloat(12.34, 'price'),
                    new DataBool(true, 'isAvailable'),
                ],
                'item',
            ),
            new DataInt(1, 'id'),
            new DataArray(
                [
                    new DataString('hello'),
                    new DataString('world'),
                ],
                'data'
            ),
        ];

        return new DataObject($object, $value);
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

        $elementData = self::elementObject($this->dataConfig, $xmlElement, $this->object);
        if (! $elementData instanceof ElementObjectInterface) {
            throw new Exception('Invalid ElementDataInterface');
        }

        return (new ElementObjectResolver())->resolve($this->dataConfig, $elementData);
    }
}
