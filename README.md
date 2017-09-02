[![Build Status](https://travis-ci.org/reinfi/zf-optimized-servicemanager.svg?branch=master)](https://travis-ci.org/reinfi/zf-optimized-servicemanager)
[![Code Climate](https://codeclimate.com/github/reinfi/zf-optimized-servicemanager/badges/gpa.svg)](https://codeclimate.com/github/reinfi/zf-optimized-servicemanager)
[![Coverage Status](https://coveralls.io/repos/github/reinfi/zf-optimized-servicemanager/badge.svg?branch=master)](https://coveralls.io/github/reinfi/zf-optimized-servicemanager?branch=master)

## Module is under development, there may be a lot of bugs at this moment.

Generate an optimized service manager for zend framework.

1. [Installation](#installation)
2. [Console commands](#console-commands)

### Installation

1. Install with Composer: `composer require reinfi/zf-optimized-servicemanager`.
2. Enable the module via ZF2 config in `appliation.config.php` under `modules` key:

```php
    return [
        'modules' => [
            'Reinfi\OptimizedServiceManager',
            // other modules
        ],
    ];
```

3. Change Application class within index.php to Reinfi\OptimizedServiceManager\Mvc\Application`
4. Use console command to generate optimized service manager.

### Console commands
* Generate servicemanager: `php public/index.php reinfi:optimize service-manager`
  Generates an optimized service manager based on your current configuration.

  Options

  --with-initializers : generate initializers if you still use them (they are deprecated)
  
  --canonicalize-names : also add canonicalized names to service manager

  --try-autowire : try autowiring during generation of service manager


### FAQ
Feel free to ask any questions or open own pull requests.