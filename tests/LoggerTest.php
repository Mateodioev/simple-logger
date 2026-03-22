<?php

declare(strict_types=1);

namespace SimpleLogger\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;
use SimpleLogger\Logger;
use SimpleLogger\streams\{LogResult, LogStream, NullStream};
use Stringable;

class LoggerTest extends TestCase
{
    private function makeCapturingStream(): object
    {
        return new class implements LogStream {
            public array $logs = [];

            public function write(LogResult $log): void
            {
                $this->logs[] = $log;
            }
        };
    }

    public function testLogWritesToStream(): void
    {
        $stream = $this->makeCapturingStream();
        $logger = new Logger(stream: $stream);

        $logger->info('Hello world');

        $this->assertCount(1, $stream->logs);
        $this->assertSame('info', $stream->logs[0]->level);
        $this->assertSame('Hello world', $stream->logs[0]->message);
    }

    public function testLogInterpolatesScalarContextValues(): void
    {
        $stream = $this->makeCapturingStream();
        $logger = new Logger(stream: $stream);

        $logger->info('User {name} is {age} years old', ['name' => 'Alice', 'age' => 30]);

        $this->assertSame('User Alice is 30 years old', $stream->logs[0]->message);
    }

    public function testLogInterpolatesBooleanContextValue(): void
    {
        $stream = $this->makeCapturingStream();
        $logger = new Logger(stream: $stream);

        $logger->info('Flag is {flag}', ['flag' => true]);

        $this->assertSame('Flag is 1', $stream->logs[0]->message);
    }

    public function testLogSkipsArrayContextValues(): void
    {
        $stream = $this->makeCapturingStream();
        $logger = new Logger(stream: $stream);

        $logger->info('Value: {data}', ['data' => ['foo', 'bar']]);

        $this->assertSame('Value: {data}', $stream->logs[0]->message);
    }

    public function testLogInterpolatesStringableObject(): void
    {
        $stream = $this->makeCapturingStream();
        $logger = new Logger(stream: $stream);

        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return 'stringable-value';
            }
        };

        $logger->info('Object: {obj}', ['obj' => $stringable]);

        $this->assertSame('Object: stringable-value', $stream->logs[0]->message);
    }

    public function testLogInterpolatesNonStringableObjectAsClassName(): void
    {
        $stream = $this->makeCapturingStream();
        $logger = new Logger(stream: $stream);

        $object = new \stdClass();
        $logger->info('Object: {obj}', ['obj' => $object]);

        $this->assertSame('Object: stdClass', $stream->logs[0]->message);
    }

    public function testLogThrowsExceptionForInvalidExceptionContext(): void
    {
        $stream = $this->makeCapturingStream();
        $logger = new Logger(stream: $stream);

        $this->expectException(InvalidArgumentException::class);
        $logger->error('Error occurred', ['exception' => 'not-a-throwable']);
    }

    public function testLogAcceptsThrowableAsExceptionContext(): void
    {
        $stream = $this->makeCapturingStream();
        $logger = new Logger(stream: $stream);

        $exception = new \RuntimeException('Runtime error');
        $logger->error('Error occurred', ['exception' => $exception]);

        $this->assertCount(1, $stream->logs);
        $this->assertSame($exception, $stream->logs[0]->exception);
    }

    public function testLogAcceptsNullExceptionContext(): void
    {
        $stream = $this->makeCapturingStream();
        $logger = new Logger(stream: $stream);

        $logger->info('No exception', ['exception' => null]);

        $this->assertCount(1, $stream->logs);
        $this->assertNull($stream->logs[0]->exception);
    }

    public function testDebugLevel(): void
    {
        $stream = $this->makeCapturingStream();
        $logger = new Logger(stream: $stream);
        $logger->debug('debug message');
        $this->assertSame(LogLevel::DEBUG, $stream->logs[0]->level);
    }

    public function testInfoLevel(): void
    {
        $stream = $this->makeCapturingStream();
        $logger = new Logger(stream: $stream);
        $logger->info('info message');
        $this->assertSame(LogLevel::INFO, $stream->logs[0]->level);
    }

    public function testNoticeLevel(): void
    {
        $stream = $this->makeCapturingStream();
        $logger = new Logger(stream: $stream);
        $logger->notice('notice message');
        $this->assertSame(LogLevel::NOTICE, $stream->logs[0]->level);
    }

    public function testWarningLevel(): void
    {
        $stream = $this->makeCapturingStream();
        $logger = new Logger(stream: $stream);
        $logger->warning('warning message');
        $this->assertSame(LogLevel::WARNING, $stream->logs[0]->level);
    }

    public function testErrorLevel(): void
    {
        $stream = $this->makeCapturingStream();
        $logger = new Logger(stream: $stream);
        $logger->error('error message');
        $this->assertSame(LogLevel::ERROR, $stream->logs[0]->level);
    }

    public function testCriticalLevel(): void
    {
        $stream = $this->makeCapturingStream();
        $logger = new Logger(stream: $stream);
        $logger->critical('critical message');
        $this->assertSame(LogLevel::CRITICAL, $stream->logs[0]->level);
    }

    public function testAlertLevel(): void
    {
        $stream = $this->makeCapturingStream();
        $logger = new Logger(stream: $stream);
        $logger->alert('alert message');
        $this->assertSame(LogLevel::ALERT, $stream->logs[0]->level);
    }

    public function testEmergencyLevel(): void
    {
        $stream = $this->makeCapturingStream();
        $logger = new Logger(stream: $stream);
        $logger->emergency('emergency message');
        $this->assertSame(LogLevel::EMERGENCY, $stream->logs[0]->level);
    }

    public function testLogWithNullStream(): void
    {
        $logger = new Logger(stream: new NullStream());
        // Should not throw any exception
        $logger->info('Should be discarded');
        $this->addToAssertionCount(1);
    }
}
