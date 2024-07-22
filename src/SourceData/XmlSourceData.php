<?php

declare(strict_types=1);

namespace DataMapper\SourceData;

use DataMapper\Elements\DataArray;
use DataMapper\Elements\DataBool;
use DataMapper\Elements\DataFloat;
use DataMapper\Elements\DataInt;
use DataMapper\Elements\DataObject;
use DataMapper\Elements\DataString;
use DataMapper\Interface\ElementObjectInterface;
use DataMapper\Resolver\DataObjectPropertyResolver;
use DataMapper\Resolver\ElementObjectResolver;
use DataMapper\Tests\MockClasses\ItemClassConstructor;
use Exception;
use SimpleXMLElement;

final class XmlSourceData extends AbstractSourceData
{
    /**
     * @throws Exception
     */
    public function coreLogic(): ElementObjectInterface
    {
        try {
            $xml = new SimpleXmlElement($this->source);
        } catch (Exception $exception) {
            throw new Exception('Invalid XML: ' . $exception->getMessage(), (int) $exception->getCode(), $exception);
        }

        (new DataObjectPropertyResolver())->resolve($this->object);

        foreach ($xml->children() as $child) {
            $name = $child->getName();
            // dump($name, $child->count(), (string) $child);
            // if (in_array($name, $dataObjectProperty->getProperties(), true)) {
            //
            // }
        }

        return new DataObject(
            $this->object,
            [
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
            ],
        );
    }

    /**
     * @throws Exception
     */
    public function resolve(): object
    {
        return (new ElementObjectResolver())->resolve($this->dataConfig, $this->coreLogic());
    }
}
