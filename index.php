<?php

declare(strict_types=1);

use SimpleLogger\Formatters\PrettyConsoleFormatter;
use SimpleLogger\Logger;
use SimpleLogger\streams\{CollectionStream, FileStream, StdoutStream};

require __DIR__ . '/vendor/autoload.php';

$logger = new Logger(stream: new CollectionStream(
    new StdoutStream(formatter: new PrettyConsoleFormatter()),
    FileStream::today(__DIR__, async: false),
));

// testing nested traces
$a = function () use ($logger): void {
    $b = fn () => $logger->info('This is an info message', ['exception' => new Exception('This is an exception')]);
    $b();
};
$a();

$logger->warning('This is a warning message');
$logger->debug('This is a debug message with {msg} using {logger}', [
    'msg' => 'parameters',
    // The objects were represented by their class name
    'logger' => $logger
]);
