<?php

declare (strict_types=1);

namespace SimpleLogger\streams;

/**
 * Black-hole stream that does nothing
 */
final class NullStream implements LogStream
{
    public function write(LogResult $log): void
    {
        // Do nothing
    }
}
