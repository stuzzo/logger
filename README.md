# Monolog Extender - Extends Monolog handlers and processors

[![Total Downloads](https://img.shields.io/packagist/dt/stuzzo/monolog-extender.svg)](https://packagist.org/packages/stuzzo/logger)
[![Latest Stable Version](https://img.shields.io/packagist/v/stuzzo/logger.svg)](https://packagist.org/packages/stuzzo/logger)

Monolog sends your logs to files, sockets, inboxes, databases and various
web services. [See the complete reference](https://github.com/Seldaek/monolog)

This library extends some Monolog's handlers and processors adding some informations such as the data or the cookies 
inside the request.

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
$log = new Logger('name');
$log->pushHandler(new StreamHandler('path/to/your.log', Logger::WARNING));

// add records to the log
$log->addWarning('Foo');
$log->addError('Bar');
```

## Documentation

- [Usage Instructions](doc/README.md)
- [Monolog Handlers, Formatters and Processors](https://github.com/Seldaek/monolog/blob/master/doc/02-handlers-formatters-processors.md)

## About

### Requirements

- This library works with PHP 5.3 or above.

### Author

Alfredo Aiello - <stuzzo@gmail.com> - <http://twitter.com/stuzzo>
