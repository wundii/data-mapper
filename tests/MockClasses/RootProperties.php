<?php

declare(strict_types=1);

namespace DataMapper\Tests\MockClasses;

final class RootProperties implements RootInterface
{
    public string $name;

    public ?int $id = null;

    public float $price;

    /**
     * @var string[]
     */
    public array $mystring = [];
}
