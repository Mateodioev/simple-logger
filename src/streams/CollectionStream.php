<?php

declare(strict_types=1);

namespace SimpleLogger\streams;

class CollectionStream implements LogStream
{
    /**
     * @var array<LogStream> $streams
     */
    private array $streams;

    public function __construct(
        LogStream ...$streams,
    ) {
        $this->streams = $streams;
    }

    public function write(LogResult $log): void
    {
        foreach ($this->streams as $stream) {
            $stream->write($log);
        }
    }
}
