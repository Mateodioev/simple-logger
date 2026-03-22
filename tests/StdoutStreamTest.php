<?php

declare(strict_types=1);

namespace SimpleLogger\Tests;

use PHPUnit\Framework\TestCase;
use SimpleLogger\streams\{LogResult, StdoutStream};

class StdoutStreamTest extends TestCase
{
    public function testWriteOutputsFormattedLog(): void
    {
        $log = new LogResult('info', 'Test output', timestamp: mktime(12, 0, 0, 1, 1, 2024));
        $stream = new StdoutStream();

        ob_start();
        $stream->write($log);
        $output = ob_get_clean();

        $this->assertStringContainsString('INFO', $output);
        $this->assertStringContainsString('Test output', $output);
    }

    public function testWriteWithCustomFormatter(): void
    {
        $formatter = new class implements \SimpleLogger\Formatters\Formatter {
            public function format(LogResult $result): string
            {
                return 'CUSTOM:' . $result->message;
            }
        };

        $stream = new StdoutStream(formatter: $formatter);
        $log = new LogResult('debug', 'Custom message');

        ob_start();
        $stream->write($log);
        $output = ob_get_clean();

        $this->assertSame('CUSTOM:Custom message', $output);
    }

    public function testWriteMultipleLogs(): void
    {
        $stream = new StdoutStream();

        ob_start();
        $stream->write(new LogResult('info', 'First'));
        $stream->write(new LogResult('error', 'Second'));
        $output = ob_get_clean();

        $this->assertStringContainsString('First', $output);
        $this->assertStringContainsString('Second', $output);
    }
}
