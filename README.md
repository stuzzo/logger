# Logger - Extends Monolog handlers and processors [![Build Status](https://img.shields.io/travis/Seldaek/monolog.svg)](https://travis-ci.org/Seldaek/monolog)

[![Total Downloads](https://img.shields.io/packagist/dt/monolog/monolog.svg)](https://packagist.org/packages/monolog/monolog)
[![Latest Stable Version](https://img.shields.io/packagist/v/monolog/monolog.svg)](https://packagist.org/packages/monolog/monolog)
[![Reference Status](https://www.versioneye.com/php/monolog:monolog/reference_badge.svg)](https://www.versioneye.com/php/monolog:monolog/references)


Monolog sends your logs to files, sockets, inboxes, databases and various
web services. See the complete list of handlers below. Special handlers
allow you to build advanced logging strategies.

This library implements the [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
interface that you can type-hint against in your own libraries to keep
a maximum of interoperability. You can also use it in your applications to
make sure you can always use another compatible logger at a later time.
As of 1.11.0 Monolog public APIs will also accept PSR-3 log levels.
Internally Monolog still uses its own level scheme since it predates PSR-3.

## Installation

Install the latest version with

```bash
$ composer require stuzzo/logger
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

- [Usage Instructions](doc/01-usage.md)
- [Handlers, Formatters and Processors](doc/02-handlers-formatters-processors.md)
- [Utility classes](doc/03-utilities.md)
- [Extending Monolog](doc/04-extending.md)

## About

### Requirements

- This library works with PHP 5.3 or above, and is also tested to work with HHVM.

### Author

Alfredo Aiello - <stuzzo@gmail.com> - <http://twitter.com/stuzzo><br />

### License

Monolog is licensed under the MIT License - see the `LICENSE` file for details
