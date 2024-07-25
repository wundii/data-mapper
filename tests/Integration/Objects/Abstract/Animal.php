<?php

declare(strict_types=1);

namespace DataMapper\Tests\Integration\Objects\Abstract;

abstract class Animal
{
    public string $name;

    public static function getClassName(string $class): string
    {
        return match ($class) {
            Dog::class => Dog::class,
            Cat::class => Dog::class,
            default => Dog::class,
        };
    }
}
