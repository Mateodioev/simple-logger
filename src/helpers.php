<?php

declare(strict_types=1);

namespace SimpleLogger;

use function array_merge;
use function implode;
use function preg_replace;

/**
 * @see https://stackoverflow.com/a/15575293
 * @param string ...$args
 * @return string
 */
function pathJoin(...$args): string
{
    $paths = [];

    foreach ($args as $arg) {
        $paths = array_merge($paths, (array) $arg);
    }

    return preg_replace('#/+#', '/', implode('/', $paths));
}
