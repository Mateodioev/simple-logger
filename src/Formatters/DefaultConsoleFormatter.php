<?php

declare(strict_types=1);

namespace SimpleLogger\Formatters;

use SimpleLogger\streams\LogResult;
use Throwable;

use function array_map;
use function date;
use function join;
use function sprintf;
use function str_repeat;
use function strlen;
use function strtoupper;

/**
 * Default output format for the console
 */
class DefaultConsoleFormatter implements Formatter
{
    protected static $format = '[%s] [%s] %s' . PHP_EOL;

    public function format(LogResult $result): string
    {
        $currentDate = $this->getCurrentDate($result->timestamp);
        $message     = sprintf(
            static::$format,
            $currentDate,
            strtoupper($result->level),
            $result->message
        );

        return $this->withExceptionMessage($result, $message, strlen($currentDate));
    }

    protected function getCurrentDate(?int $timestamp = null): string
    {
        return date('Y-m-d H:i:s', timestamp: $timestamp);
    }

    /**
     * Add the exception message to the log message
     */
    protected function withExceptionMessage(LogResult $result, string $message, int $padding): string
    {
        if ($result->exception === null) {
            return $message;
        }

        return $message . $this->getExceptionMessage($result->exception, $padding + 3);
    }

    private function getExceptionMessage(Throwable $e, int $padding): string
    {
        $strPadding = str_repeat(' ', $padding);
        return $strPadding . 'Caused by: ' . $e::class . ' (' . $e->getMessage() . ') '
            . PHP_EOL . $strPadding . 'Stack trace: ' .
            $this->formatTraceException($e, $padding) . PHP_EOL;
    }

    private function formatTraceException(Throwable $e, int $padding): string
    {
        $padding     = str_repeat(' ', $padding + 2);
        $traceFormat = $padding . '#%d %s:%s %s';
        $i           = 0;

        $messages = array_map(function ($trace) use (&$i, $traceFormat): string {
            ++$i;
            return PHP_EOL . sprintf(
                $traceFormat,
                $i,
                $trace['file'] ?? '[internal function]',
                (string) ($trace['line'] ?? ''),
                $trace['function'],
            );
        }, $e->getTrace());

        return join('', $messages);
    }
}
