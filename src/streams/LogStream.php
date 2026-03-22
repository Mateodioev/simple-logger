<?php

declare (strict_types=1);

namespace SimpleLogger\streams;

/**
 * Where send the logs
 */
interface LogStream
{
    /**
     * Write a message
     */
    public function write(LogResult $log): void;
}
