<?php

declare(strict_types=1);

namespace SimpleLogger\Tests;

use PHPUnit\Framework\TestCase;
use SimpleLogger\streams\{LogResult, NullStream};

class NullStreamTest extends TestCase
{
    public function testWriteDoesNotThrow(): void
    {
        $stream = new NullStream();
        $log = new LogResult('info', 'Test message');

        $stream->write($log);

        // If we reach here, no exception was thrown
        $this->addToAssertionCount(1);
    }

    public function testWriteMultipleTimesDoesNotThrow(): void
    {
        $stream = new NullStream();

        $stream->write(new LogResult('debug', 'Debug'));
        $stream->write(new LogResult('info', 'Info'));
        $stream->write(new LogResult('error', 'Error', new \RuntimeException('err')));

        $this->addToAssertionCount(1);
    }
}
