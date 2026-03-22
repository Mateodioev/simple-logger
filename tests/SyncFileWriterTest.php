<?php

declare(strict_types=1);

namespace SimpleLogger\Tests;

use PHPUnit\Framework\TestCase;
use SimpleLogger\data\SyncFileWriter;

class SyncFileWriterTest extends TestCase
{
    private string $tmpFile;

    protected function setUp(): void
    {
        $this->tmpFile = tempnam(sys_get_temp_dir(), 'simple-logger-test-');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
    }

    public function testWriteCreatesContent(): void
    {
        $writer = new SyncFileWriter($this->tmpFile);
        $writer->write('Hello, World!' . PHP_EOL);

        $this->assertStringContainsString('Hello, World!', file_get_contents($this->tmpFile));
    }

    public function testWriteAppendsContent(): void
    {
        $writer = new SyncFileWriter($this->tmpFile);
        $writer->write('First line' . PHP_EOL);
        $writer->write('Second line' . PHP_EOL);

        $content = file_get_contents($this->tmpFile);
        $this->assertStringContainsString('First line', $content);
        $this->assertStringContainsString('Second line', $content);
    }

    public function testWriteEmptyString(): void
    {
        $writer = new SyncFileWriter($this->tmpFile);
        $writer->write('');

        $this->assertSame('', file_get_contents($this->tmpFile));
    }

    public function testWriteMultilineContent(): void
    {
        $writer = new SyncFileWriter($this->tmpFile);
        $content = "line1\nline2\nline3\n";
        $writer->write($content);

        $this->assertSame($content, file_get_contents($this->tmpFile));
    }
}
