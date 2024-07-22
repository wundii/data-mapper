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
use Exception;

final class XmlSourceData extends AbstractSourceData
{
    public function coreLogic(): ElementObjectInterface
    {
        return new DataObject(
            $this->objectName,
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
        $objectElementResolver = new ElementObjectResolver($this->dataConfig, $this->coreLogic());
        return $objectElementResolver->resolve();
    }
}
