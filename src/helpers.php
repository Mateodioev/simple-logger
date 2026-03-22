<?php

declare(strict_types=1);

namespace SimpleLogger;

use function array_merge;
use function join;
use function preg_replace;

/**
 * @see https://stackoverflow.com/a/15575293
 * @param string ...$args
 * @return string
 */
function pathJoin(...$args)
{
    $paths = [];

    foreach ($args as $arg) {
        $paths = array_merge($paths, (array) $arg);
    }

    return preg_replace('#/+#', '/', join('/', $paths));
}
