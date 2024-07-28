<?php

declare(strict_types=1);

namespace Integration\Objects\ApproachBasic;

final class BaseProperty
{
    public float $amount;

    public string $name;

    public ?int $id = null;

    /**
     * @var string[]
     */
    public array $myStrings = [];

    public ?SubProperty $subProperty;

    /**
     * @var SubProperty[]
     */
    public array $subProperties = [];
}
