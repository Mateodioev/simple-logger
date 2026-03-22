<?php

declare(strict_types=1);

namespace SimpleLogger\streams;

use SimpleLogger\Formatters\{DefaultConsoleFormatter, Formatter};

/**
 * Write log messages to the standard output.
 */
class StdoutStream implements LogStream
{
    public function __construct(
        private Formatter $formatter = new DefaultConsoleFormatter(),
    ) {
    }
    public function write(LogResult $log): void
    {
        print($this->formatter->format($log));
    }
}
