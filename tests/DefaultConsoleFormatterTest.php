<?php

declare(strict_types=1);

namespace SimpleLogger\Tests;

use PHPUnit\Framework\TestCase;
use SimpleLogger\Formatters\DefaultConsoleFormatter;
use SimpleLogger\streams\LogResult;

class DefaultConsoleFormatterTest extends TestCase
{
    private DefaultConsoleFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new DefaultConsoleFormatter();
    }

    public function testFormatContainsTimestampAndLevel(): void
    {
        $timestamp = mktime(10, 30, 0, 1, 1, 2024);
        $log = new LogResult('info', 'Hello', timestamp: $timestamp);

        $result = $this->formatter->format($log);

        $this->assertStringContainsString('2024-01-01 10:30:00', $result);
        $this->assertStringContainsString('INFO', $result);
        $this->assertStringContainsString('Hello', $result);
    }

    public function testFormatUppercasesLevel(): void
    {
        $log = new LogResult('warning', 'msg', timestamp: time());

        $result = $this->formatter->format($log);

        $this->assertStringContainsString('WARNING', $result);
    }

    public function testFormatEndsWithNewline(): void
    {
        $log = new LogResult('debug', 'msg', timestamp: time());

        $result = $this->formatter->format($log);

        $this->assertStringEndsWith(PHP_EOL, $result);
    }

    public function testFormatWithoutExceptionHasNoExceptionInfo(): void
    {
        $log = new LogResult('notice', 'A notice', timestamp: time());

        $result = $this->formatter->format($log);

        $this->assertStringNotContainsString('Caused by:', $result);
    }

    public function testFormatWithExceptionIncludesExceptionDetails(): void
    {
        $exception = new \InvalidArgumentException('Bad argument');
        $log = new LogResult('error', 'An error', $exception, time());

        $result = $this->formatter->format($log);

        $this->assertStringContainsString('Caused by:', $result);
        $this->assertStringContainsString('InvalidArgumentException', $result);
        $this->assertStringContainsString('Bad argument', $result);
        $this->assertStringContainsString('Stack trace:', $result);
    }

    public function testFormatWithExceptionIncludesStackTraceEntries(): void
    {
        $exception = new \RuntimeException('Trace test');
        $log = new LogResult('critical', 'Critical error', $exception, time());

        $result = $this->formatter->format($log);

        $this->assertStringContainsString('#1', $result);
    }
}
