<?php

declare(strict_types=1);

namespace SimpleLogger\Tests;

use PHPUnit\Framework\TestCase;
use SimpleLogger\Formatters\DefaultFormatter;
use SimpleLogger\streams\LogResult;

class DefaultFormatterTest extends TestCase
{
    private DefaultFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new DefaultFormatter();
    }

    public function testFormatProducesExpectedOutput(): void
    {
        $timestamp = mktime(12, 0, 0, 6, 15, 2024);
        $log = new LogResult('info', 'Test message', timestamp: $timestamp);

        $result = $this->formatter->format($log);

        $this->assertStringContainsString('[2024-06-15 12:00:00]', $result);
        $this->assertStringContainsString('[INFO]', $result);
        $this->assertStringContainsString('Test message', $result);
        $this->assertStringEndsWith(PHP_EOL, $result);
    }

    public function testFormatUppercasesLevel(): void
    {
        $log = new LogResult('debug', 'msg', timestamp: time());

        $result = $this->formatter->format($log);

        $this->assertStringContainsString('[DEBUG]', $result);
        $this->assertStringNotContainsString('[debug]', $result);
    }

    public function testFormatWithoutExceptionHasNoStackTrace(): void
    {
        $log = new LogResult('warning', 'A warning', timestamp: time());

        $result = $this->formatter->format($log);

        $this->assertStringNotContainsString('Caused by:', $result);
    }

    public function testFormatWithExceptionIncludesExceptionDetails(): void
    {
        $exception = new \RuntimeException('Something failed');
        $log = new LogResult('error', 'Error occurred', $exception, time());

        $result = $this->formatter->format($log);

        $this->assertStringContainsString('Caused by:', $result);
        $this->assertStringContainsString('RuntimeException', $result);
        $this->assertStringContainsString('Something failed', $result);
    }

    public function testFormatWithExceptionIncludesStackTrace(): void
    {
        $exception = new \RuntimeException('Trace test');
        $log = new LogResult('critical', 'msg', $exception, time());

        $result = $this->formatter->format($log);

        $this->assertStringContainsString('#0', $result);
    }

    public function testFormatAllLogLevels(): void
    {
        $levels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];

        foreach ($levels as $level) {
            $log = new LogResult($level, 'Test', timestamp: time());
            $result = $this->formatter->format($log);
            $this->assertStringContainsString('[' . strtoupper($level) . ']', $result);
        }
    }
}
