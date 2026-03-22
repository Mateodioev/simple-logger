<?php

declare(strict_types=1);

namespace SimpleLogger\Tests;

use PHPUnit\Framework\TestCase;
use SimpleLogger\streams\LogResult;
use Throwable;

class LogResultTest extends TestCase
{
    public function testConstructorSetsFields(): void
    {
        $log = new LogResult('info', 'Test message');

        $this->assertSame('info', $log->level);
        $this->assertSame('Test message', $log->message);
        $this->assertNull($log->exception);
    }

    public function testConstructorSetsTimestampAutomatically(): void
    {
        $before = time();
        $log = new LogResult('debug', 'msg');
        $after = time();

        $this->assertGreaterThanOrEqual($before, $log->timestamp);
        $this->assertLessThanOrEqual($after, $log->timestamp);
    }

    public function testConstructorAcceptsCustomTimestamp(): void
    {
        $timestamp = 1700000000;
        $log = new LogResult('warning', 'msg', timestamp: $timestamp);

        $this->assertSame($timestamp, $log->timestamp);
    }

    public function testConstructorAcceptsException(): void
    {
        $exception = new \RuntimeException('Something went wrong');
        $log = new LogResult('error', 'An error occurred', exception: $exception);

        $this->assertSame($exception, $log->exception);
        $this->assertInstanceOf(Throwable::class, $log->exception);
    }

    public function testConstructorWithAllParameters(): void
    {
        $exception = new \Exception('test');
        $timestamp = 1700000000;
        $log = new LogResult('critical', 'Critical error', $exception, $timestamp);

        $this->assertSame('critical', $log->level);
        $this->assertSame('Critical error', $log->message);
        $this->assertSame($exception, $log->exception);
        $this->assertSame($timestamp, $log->timestamp);
    }
}
