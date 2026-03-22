<?php

declare(strict_types=1);

namespace SimpleLogger\Formatters;

use SimpleLogger\streams\LogResult;
use Throwable;

use function date;
use function sprintf;
use function strtoupper;

/**
 * Format the log result with this format:
 * TIME LEVEL MESSAGE
 * [STACKTRACE]
 */
final class DefaultFormatter implements Formatter
{
    public function format(LogResult $result): string
    {
        $message = sprintf(
            '[%s] [%s] %s' . PHP_EOL,
            date('Y-m-d H:i:s', $result->timestamp),
            strtoupper($result->level),
            $result->message,
        );

        if ($result->exception !== null) {
            $message .= $this->getExceptionMessage($result->exception);
        }

        return $message;
    }

    private function getExceptionMessage(Throwable $exception): string
    {
        return 'Caused by: ' . $exception::class . ' (' . $exception->getMessage() . ') '
            . PHP_EOL . $exception->getTraceAsString() . PHP_EOL;
    }
}
