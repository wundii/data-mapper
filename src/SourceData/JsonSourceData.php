<?php

declare(strict_types=1);

namespace DataMapper\SourceData;

use DataMapper\Elements\DataInt;
use DataMapper\Elements\DataObject;
use DataMapper\Elements\DataString;
use DataMapper\Interface\ObjectElementInterface;

final class JsonSourceData extends AbstractSourceData
{
    public function coreLogic(): ObjectElementInterface
    {
        return new DataObject(
            $this->objectName,
            [
                new DataString('constructor'),
                new DataInt(1),
            ],
        )
        ;
    }

    public function resolve(): object
    {
        return new \stdClass();
    }
}
