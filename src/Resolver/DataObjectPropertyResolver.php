<?php

declare(strict_types=1);

namespace DataMapper\Resolver;

use DataMapper\DataObjectProperty;
use Exception;
use InvalidArgumentException;
use ReflectionClass;

final readonly class DataObjectPropertyResolver
{
    /**
     * @throws Exception
     */
    public function resolve(string|object $object): DataObjectProperty
    {
        if (! is_object($object) && ! class_exists($object) && ! interface_exists($object)) {
            throw new InvalidArgumentException(sprintf('object %s does not exist', $object));
        }

        $properties = [];
        $setters = [];

        $reflectionClass = new ReflectionClass($object);
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $properties[] = $reflectionProperty->getName();
        }

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            if (str_starts_with($reflectionMethod->getName(), 'set')) {
                $setters[] = $reflectionMethod->getName();
            }
        }

        return new DataObjectProperty(
            $properties,
            $setters,
        );
    }
}
