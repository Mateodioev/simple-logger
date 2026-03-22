<?php

declare(strict_types=1);

namespace SimpleLogger\Tests;

use PHPUnit\Framework\TestCase;
use SimpleLogger\Formatters\DefaultFormatter;
use SimpleLogger\streams\{FileStream, LogResult};

class FileStreamTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/simple-logger-test-' . uniqid();
        mkdir($this->tmpDir, 0777, true);
    }

    protected function tearDown(): void
    {
        $files = glob($this->tmpDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        rmdir($this->tmpDir);
    }

    public function testSyncCreatesFileAndWritesLog(): void
    {
        $filePath = $this->tmpDir . '/test.log';
        $stream = FileStream::sync($filePath);

        $log = new LogResult('info', 'File log message', timestamp: time());
        $stream->write($log);

        $this->assertFileExists($filePath);
        $content = file_get_contents($filePath);
        $this->assertStringContainsString('File log message', $content);
        $this->assertStringContainsString('[INFO]', $content);
    }

    public function testSyncAppendsMultipleLogs(): void
    {
        $filePath = $this->tmpDir . '/append.log';
        $stream = FileStream::sync($filePath);

        $stream->write(new LogResult('info', 'First log', timestamp: time()));
        $stream->write(new LogResult('debug', 'Second log', timestamp: time()));

        $content = file_get_contents($filePath);
        $this->assertStringContainsString('First log', $content);
        $this->assertStringContainsString('Second log', $content);
    }

    public function testSyncWithCustomFormatter(): void
    {
        $filePath = $this->tmpDir . '/custom.log';
        $formatter = new DefaultFormatter();
        $stream = FileStream::sync($filePath, $formatter);

        $timestamp = mktime(0, 0, 0, 3, 15, 2024);
        $log = new LogResult('warning', 'Custom formatter', timestamp: $timestamp);
        $stream->write($log);

        $content = file_get_contents($filePath);
        $this->assertStringContainsString('2024-03-15', $content);
        $this->assertStringContainsString('WARNING', $content);
        $this->assertStringContainsString('Custom formatter', $content);
    }

    public function testTodayCreatesTodayDateFilename(): void
    {
        $stream = FileStream::today($this->tmpDir, async: false);
        $log = new LogResult('info', 'Today log', timestamp: time());
        $stream->write($log);

        $expectedFile = $this->tmpDir . '/' . date('Y-m-d') . '.log';
        $this->assertFileExists($expectedFile);
    }

    public function testSyncWithExceptionIncludesExceptionInFile(): void
    {
        $filePath = $this->tmpDir . '/exception.log';
        $stream = FileStream::sync($filePath);
        $exception = new \RuntimeException('File exception');

        $stream->write(new LogResult('error', 'Error with exception', $exception, time()));

        $content = file_get_contents($filePath);
        $this->assertStringContainsString('RuntimeException', $content);
        $this->assertStringContainsString('File exception', $content);
    }
}
