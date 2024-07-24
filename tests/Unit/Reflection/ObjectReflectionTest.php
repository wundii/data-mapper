<?php

declare(strict_types=1);

namespace DataMapper\Tests\Unit\Reflection;

use DataMapper\Enum\ApproachEnum;
use DataMapper\Reflection\AnnotationReflection;
use DataMapper\Reflection\ObjectReflection;
use DataMapper\Reflection\PropertyReflection;
use PHPUnit\Framework\TestCase;

class ObjectReflectionTest extends TestCase
{
    public function objectEmpty(): ObjectReflection
    {
        return new ObjectReflection([], [], []);
    }

    public function objectComplex(): ObjectReflection
    {
        $annotationReflection = new AnnotationReflection(
            [],
            [],
        );

        return new ObjectReflection(
            [
                new PropertyReflection('nameProperty', ['string', 'null', 'float'], $annotationReflection),
                new PropertyReflection('dataProperty', ['array'], $annotationReflection),
                new PropertyReflection('itemProperty', ['DataMapper\Tests\MockClasses\ItemConstructor'], $annotationReflection),
            ],
            [
                new PropertyReflection('nameConstructor', ['string', 'null', 'float'], $annotationReflection),
                new PropertyReflection('dataConstructor', ['array'], $annotationReflection),
                new PropertyReflection('itemConstructor', ['DataMapper\Tests\MockClasses\ItemConstructor'], $annotationReflection),
            ],
            [
                new PropertyReflection('nameSetter', ['string', 'null', 'float'], $annotationReflection),
                new PropertyReflection('dataSetter', ['array'], $annotationReflection),
                new PropertyReflection('itemSetter', ['DataMapper\Tests\MockClasses\ItemConstructor'], $annotationReflection),
            ],
        );
    }

    public function testFindEmpty(): void
    {
        $object = $this->objectEmpty();

        $this->assertNull($object->find(ApproachEnum::PROPERTY, 'nameProperty'));
        $this->assertNull($object->find(ApproachEnum::PROPERTY, 'NAMEPROPERTY'));
        $this->assertNull($object->find(ApproachEnum::CONSTRUCTOR, 'dataConstructor'));
        $this->assertNull($object->find(ApproachEnum::CONSTRUCTOR, 'DATACONSTRUCTOR'));
        $this->assertNull($object->find(ApproachEnum::SETTER, 'itemSetter'));
        $this->assertNull($object->find(ApproachEnum::SETTER, 'ITEMSETTER'));
    }

    public function testFind(): void
    {
        $object = $this->objectComplex();

        $this->assertInstanceOf(PropertyReflection::class, $object->find(ApproachEnum::PROPERTY, 'nameProperty'));
        $this->assertSame('nameProperty', $object->find(ApproachEnum::PROPERTY, 'nameProperty')?->getName());
        $this->assertSame('nameProperty', $object->find(ApproachEnum::PROPERTY, 'NAMEPROPERTY')?->getName());
        $this->assertInstanceOf(PropertyReflection::class, $object->find(ApproachEnum::CONSTRUCTOR, 'dataConstructor'));
        $this->assertSame('dataConstructor', $object->find(ApproachEnum::CONSTRUCTOR, 'dataConstructor')?->getName());
        $this->assertSame('dataConstructor', $object->find(ApproachEnum::CONSTRUCTOR, 'DATACONSTRUCTOR')?->getName());
        $this->assertInstanceOf(PropertyReflection::class, $object->find(ApproachEnum::SETTER, 'itemSetter'));
        $this->assertSame('itemSetter', $object->find(ApproachEnum::SETTER, 'itemSetter')?->getName());
        $this->assertSame('itemSetter', $object->find(ApproachEnum::SETTER, 'ITEMSETTER')?->getName());
    }
}
