<?php

declare(strict_types=1);

namespace Integration\Objects\ApproachBasic;

use Wundii\DataMapper\Attribute\TargetData;

final class AttributeTargetProperty
{
    #[TargetData('amount')]
    public float $costs;

    #[TargetData('name')]
    public string $title;

    #[TargetData('id')]
    public ?int $primaryId = null;

    /**
     * @var string[]
     */
    #[TargetData('myStrings')]
    public array $strings = [];

    #[TargetData('subProperty')]
    public ?SubProperty $subProp;

    /**
     * @var SubProperty[]
     */
    #[TargetData('subProperties')]
    public array $subProps = [];
}
