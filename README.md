# README #

This library can re-format json log. The single line formatter is based on the [Datenkraft\MonologGkeFormatter\GkeFormatter](https://github.com/datenkraft/monolog-gke-formatter)
and is optimized for the Google Kubernetes Engine, whereas the multi line formatter is for local logging with beautified
output.

Both introduced formatter convert objects which would not be convertible by monolog to arrays. Please be aware that
through this feature private data could be exposed to log if not adding it to the blacklist.

## Installation

```
composer require Datenkraft/monolog-formatter
```

## Usage

```php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Datenkraft\MonologFormatter;

$handler = new StreamHandler('php://stdout');
$handler->setFormatter(new SingleLineFormatter());
// or
$handler->setFormatter(new MultiLineFormatter());
```
