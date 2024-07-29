<?php

declare(strict_types=1);

use Wundii\PHPLint\Config\LintConfig;

return static function (LintConfig $lintConfig): void {
    $lintConfig->cacheDirectory('cache/phplint');
    $lintConfig->paths([
        'src',
        'tests',
    ]);
};