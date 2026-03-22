<?php

declare(strict_types=1);

namespace SimpleLogger\streams;

use SimpleLogger\data\{AsyncFileWriter, FileWriter, SyncFileWriter};
use SimpleLogger\Formatters\{DefaultFormatter, Formatter};

use function date;
use function SimpleLogger\pathJoin;

/**
 * Write content to a file
 */
class FileStream implements LogStream
{
    /**
     * Create a new FileStream with the current date as the filename
     */
    public static function today(string $dir, bool $async = false, ?Formatter $formatter = null): FileStream
    {
        $filepath = pathJoin($dir, date('Y-m-d') . '.log');
        if ($async) {
            return static::async($filepath, $formatter);
        }

        return static::sync($filepath, $formatter);
    }

    public static function async(string $filePath, ?Formatter $formatter = null): FileStream
    {
        return new self(new AsyncFileWriter($filePath), $formatter);
    }

    public static function sync(string $filePath, ?Formatter $formatter = null): FileStream
    {
        return new self(new SyncFileWriter($filePath), $formatter);
    }

    public function __construct(
        private FileWriter $writer,
        private ?Formatter $formatter,
    ) {
        $this->formatter ??= new DefaultFormatter();
    }

    public function write(LogResult $log): void
    {
        $this->writer->write($this->formatter->format($log));
    }
}
