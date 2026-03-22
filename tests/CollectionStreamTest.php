<?php

declare(strict_types=1);

namespace SimpleLogger\Tests;

use PHPUnit\Framework\TestCase;
use SimpleLogger\streams\{CollectionStream, LogResult, LogStream, NullStream};

class CollectionStreamTest extends TestCase
{
    private function makeCapturingStream(): LogStream
    {
        return new class implements LogStream {
            public array $logs = [];

            public function write(LogResult $log): void
            {
                $this->logs[] = $log;
            }
        };
    }

    public function testWriteCallsAllStreams(): void
    {
        $stream1 = $this->makeCapturingStream();
        $stream2 = $this->makeCapturingStream();
        $collection = new CollectionStream($stream1, $stream2);

        $log = new LogResult('info', 'Test');
        $collection->write($log);

        $this->assertCount(1, $stream1->logs);
        $this->assertCount(1, $stream2->logs);
        $this->assertSame($log, $stream1->logs[0]);
        $this->assertSame($log, $stream2->logs[0]);
    }

    public function testWriteWithSingleStream(): void
    {
        $stream = $this->makeCapturingStream();
        $collection = new CollectionStream($stream);

        $log = new LogResult('debug', 'Debug message');
        $collection->write($log);

        $this->assertCount(1, $stream->logs);
        $this->assertSame($log, $stream->logs[0]);
    }

    public function testWriteWithNoStreams(): void
    {
        $collection = new CollectionStream();

        // Should not throw
        $collection->write(new LogResult('info', 'Ignored'));
        $this->addToAssertionCount(1);
    }

    public function testWriteMultipleLogsToAllStreams(): void
    {
        $stream1 = $this->makeCapturingStream();
        $stream2 = $this->makeCapturingStream();
        $stream3 = $this->makeCapturingStream();
        $collection = new CollectionStream($stream1, $stream2, $stream3);

        $collection->write(new LogResult('info', 'First'));
        $collection->write(new LogResult('error', 'Second'));

        $this->assertCount(2, $stream1->logs);
        $this->assertCount(2, $stream2->logs);
        $this->assertCount(2, $stream3->logs);
    }

    public function testCollectionCanContainNullStream(): void
    {
        $capturing = $this->makeCapturingStream();
        $collection = new CollectionStream($capturing, new NullStream());

        $log = new LogResult('warning', 'Warning');
        $collection->write($log);

        $this->assertCount(1, $capturing->logs);
    }
}
