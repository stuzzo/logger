# Monolog Extender - Extends Monolog handlers and processors

[![Total Downloads](https://img.shields.io/packagist/dt/stuzzo/monolog-extender.svg)](https://packagist.org/packages/stuzzo/monolog-extender)
[![Latest Stable Version](https://img.shields.io/packagist/v/stuzzo/monolog-extender.svg)](https://packagist.org/packages/stuzzo/monolog-extender)

Monolog sends your logs to files, sockets, inboxes, databases and various
web services. [See the complete reference](https://github.com/Seldaek/monolog)

This library extends Monolog's handlers and processors adding data to the record generated from processors.
Furthermore the library improves logs format. 

## Installation

Install the latest version with

```bash
$ composer require stuzzo/monolog-extender
```

## Basic Usage

```php
<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('request');

$handler = new StreamHandler('path/to/your.log', Logger::WARNING);

$formatter = new \Stuzzo\Monolog\Formatter\StreamFormatter(null, 'Y-m-d H:i:s');
$handler->setFormatter($formatter);
$log->pushHandler($handler);

try {
    throw new \RuntimeException('Something happen');
} catch (\Exception $exception) {
    $log->critical('Error', ['exception' => $exception]);
}
```

## Documentation

- [Usage Instructions](doc/README.md)
- [Monolog Handlers, Formatters and Processors](https://github.com/Seldaek/monolog/blob/master/doc/02-handlers-formatters-processors.md)

## About

### Requirements

- This library works with PHP 5.5.9 or above.

### Author

Alfredo Aiello - <stuzzo@gmail.com> - <http://twitter.com/stuzzo>
