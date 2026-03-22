<?php

declare (strict_types=1);

namespace SimpleLogger\data;

use Amp\File;
use RuntimeException;

/**
 * Write content to a file using https://amphp.org/file
 */
class AsyncFileWriter implements FileWriter
{
    public function __construct(private string $filename)
    {
        if (!interface_exists(File\FilesystemDriver::class)) {
            throw new RuntimeException('Please install amphp/file to use this class or use ' . SyncFileWriter::class);
        }
    }

    public function write(string $content): void
    {
        if ($this->existsFile()) {
            $content = File\read($this->filename) . $content;
        }

        File\write($this->filename, $content);
    }

    private function existsFile(): bool
    {
        return File\exists($this->filename);
    }
}
