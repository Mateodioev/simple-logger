<?php

declare (strict_types=1);

namespace SimpleLogger\data;

use function file_put_contents;

/**
 * Write content to a file
 */
class SyncFileWriter implements FileWriter
{
    public function __construct(private string $filename)
    {
    }

    public function write(string $content): void
    {
        file_put_contents($this->filename, $content, FILE_APPEND | LOCK_EX);
    }
}
