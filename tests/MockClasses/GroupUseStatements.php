<?php

declare(strict_types=1);

namespace MockClasses;

use MockClasses\Sub\SubItemConstructor;
use Wundii\DataMapper\Dto\{AnnotationDto, ParameterDto};
use function array_map;
use const PHP_EOL;

/**
 * Fixture for the use statement parser: group use, use function and use const.
 * Every import is used on purpose so that no coding standard removes it.
 */
class GroupUseStatements
{
    public function __construct(
        private ?SubItemConstructor $subItemConstructor = null,
    ) {
    }

    public function getSubItemConstructor(): ?SubItemConstructor
    {
        return $this->subItemConstructor;
    }

    public function getAnnotationDto(): AnnotationDto
    {
        $parameterDtos = array_map(
            static fn (string $name): ParameterDto => new ParameterDto($name, ['string']),
            ['first', 'second'],
        );

        return new AnnotationDto($parameterDtos, []);
    }

    public function getSeparator(): string
    {
        return PHP_EOL;
    }
}
