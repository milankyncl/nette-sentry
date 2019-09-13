# Sentry bridge for [Nette](https://www.nette.org) framework

## Prolog

First of all - I do not own the original idea of creating this repository, I forked the code from [nofutur3/nette-sentry](https://github.com/nofutur3/nette-sentry) repository. I updated this repository and posted it to Github for better purpose.

Nette integration for Sentry

## Installation

The recommended installation is using [composer](https://getcomposer.org/). 

```
composer require milankyncl/nette-sentry
```

## Usage

##### With Nette (2.3+)

```
extensions:
    sentry: MilanKyncl\Nette\Sentry\DI\SentryExtension
    
sentry:
    dsn: (your dsn from sentry)
```

#### Extended configuration with default values

```
sentry:
    dsn: (your dsn from sentry)
    options: # check: https://docs.sentry.io/clients/php/config/
```