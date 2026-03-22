<?php

declare(strict_types=1);

namespace SimpleLogger;

use Psr\Log\{AbstractLogger, InvalidArgumentException};
use SimpleLogger\streams\{LogResult, LogStream};
use Stringable;
use Throwable;

class Logger extends AbstractLogger
{
    public function __construct(private LogStream $stream)
    {
    }

    /**
     * @inheritDoc
     * @param string $level
     */
    public function log($level, string | Stringable $message, array $context = []): void
    {
        if (($context['exception'] ?? null) != null && ($context['exception'] instanceof Throwable) == false) {
            throw new InvalidArgumentException('The exception must be an instance of Throwable');
        }

        $logMessage = new LogResult(
            level: $level,
            message: $this->interpolate($message, $context),
            exception: $context['exception'] ?? null,
        );

        $this->stream->write($logMessage);
    }

    /**
     * Interpolates context values into the message placeholders.
     */
    private function interpolate(string $message, array $context): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (is_array($val)) {
                continue;
            }
            if (is_scalar($val)) {
                $replace['{' . $key . '}'] = (string) $val;
                continue;
            }
            if (is_object($val)) {
                $replace['{' . $key . '}'] = $this->objectToString($val);
            }
        }

        return strtr($message, $replace);
    }

    private function objectToString(mixed $val): string
    {
        if ($val instanceof Stringable) {
            return $val->__toString();
        }

        if (is_array($val)) {
            return json_encode($val, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        }

        if (is_object($val)) {
            return $val::class;
        }

        return '""';
    }
}
