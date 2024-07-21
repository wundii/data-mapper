<?php

declare(strict_types=1);

namespace DataMapper\SourceData;

use DataMapper\Elements\DataInt;
use DataMapper\Elements\DataObject;
use DataMapper\Elements\DataString;
use DataMapper\Interface\ObjectElementInterface;
use DataMapper\Resolver\ObjectElementResolver;
use Exception;

final class XmlSourceData extends AbstractSourceData
{
    public function coreLogic(): ObjectElementInterface
    {
        return new DataObject(
            $this->objectName,
            [
                new DataString('constructor', 'name'),
                new DataInt(1, 'id'),
            ],
        );
    }

    /**
     * @throws Exception
     */
    public function resolve(): object
    {
        $objectElementResolver = new ObjectElementResolver($this->dataConfig, $this->coreLogic());
        return $objectElementResolver->resolve();
    }
}
