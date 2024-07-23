<?php

declare(strict_types=1);

namespace DataMapper\Tests\MockClasses;

final class AnnotationProperty implements RootInterface
{
    private string $name;

    /**
     * @var ItemConstructor[]
     */
    private array $item;
}
