This library is intended to interact when a message is at least at error level.

# Using processor

You can add a processor to an handler or to a logger.

```php
$log = new Logger('request');

$handler = new StreamHandler('path/to/your.log', Logger::WARNING);
$formatter = new \Stuzzo\Monolog\Formatter\StreamFormatter(null, 'Y-m-d H:i:s');
$handler->setFormatter($formatter);
$log->pushHandler($handler);
$log->pushProcessor(new \Stuzzo\Monolog\Processor\ExtendedWebProcessor());
```

# Using formatters

To use a formatter, just add to handler

```php
// create a log channel
$log = new Logger('request');

$handler = new StreamHandler('path/to/your.log', Logger::WARNING);
$formatter = new \Stuzzo\Monolog\Formatter\StreamFormatter(null, 'Y-m-d H:i:s');
$handler->setFormatter($formatter);
$log->pushHandler($handler);
```

