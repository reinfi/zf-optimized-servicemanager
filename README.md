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

### Console commands
* Generate servicemanager: `php public/index.php reinfi:optimize service-manager`
  Generates an optimized service manager based on your current configuration.

### FAQ
Feel free to ask any questions or open own pull requests.