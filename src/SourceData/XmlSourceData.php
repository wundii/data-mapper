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
use DataMapper\Resolver\ElementObjectResolver;
use DataMapper\Tests\MockClasses\ItemClassConstructor;
use DOMDocument;
use Exception;

final class XmlSourceData extends AbstractSourceData
{
    /**
     * @throws Exception
     */
    public function coreLogic(): ElementObjectInterface
    {
        libxml_use_internal_errors(true);

        $domDocument = new DOMDocument();
        $domDocument->loadXML($this->source);

        $domErrors = libxml_get_errors();
        if ($domErrors !== []) {
            $exceptionMessage = implode("\n", array_map(static function ($error): string {
                return $error->message;
            }, $domErrors));
            throw new Exception('Invalid XML: ' . $exceptionMessage);
        }

        // dump($dom);

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
        $elementObjectResolver = new ElementObjectResolver($this->dataConfig, $this->coreLogic());
        return $elementObjectResolver->resolve();
    }
}
