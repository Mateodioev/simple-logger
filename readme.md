# Simple psr logger

## Installation

```bash
composer require kombyte/simple-logger
```

## Usage

```php
use SimpleLogger\Logger;
use SimpleLogger\streams\{CollectionStream, FileStream, StdoutStream};

$logger = new Logger(stream: new CollectionStream([
    new StdoutStream(),
    FileStream::async(__DIR__ . '/log.log'),
]));

$logger->debug('The debug message');
```

### Creating a new stream

A stream is a class that implements the `SimpleLogger\streams\LogStream` interface. You can create your own stream by implementing the `write` method.

```php
use SimpleLogger\streams\LogStream;

class MyStream implements LogStream
{
    public function write(LogResult $message): void
    {
        // Write the message
    }
}
```
