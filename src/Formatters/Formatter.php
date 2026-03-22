<?php

declare(strict_types=1);

namespace SimpleLogger\Formatters;

use SimpleLogger\streams\LogResult;

interface Formatter
{
    /**
     * Format the log result
     */
    public function format(LogResult $result): string;
}
