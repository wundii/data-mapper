<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return ECSConfig::configure()
    ->withCache('./cache/ecs')
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withParallel()
    // this way you add a single rule
    ->withRules([
        NoUnusedImportsFixer::class,
    ])
    // this way you can add sets - group of rules
    ->withSets([
        SetList::ARRAY,
        SetList::CLEAN_CODE,
        SetList::COMMENTS,
        SetList::COMMON,
        SetList::CONTROL_STRUCTURES,
        SetList::DOCBLOCK,
        SetList::NAMESPACES,
        SetList::PSR_12,
        SetList::SPACES,
    ]);
