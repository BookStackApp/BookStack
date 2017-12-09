# Laravel BrowserKit Testing

This package provides a backwards compatibility layer for Laravel 5.3 style "BrowserKit" testing on Laravel 5.4.

## Installation

First, install this package:

    composer require laravel/browser-kit-testing --dev

Next, modify your application's base `TestCase` class to extend `Laravel\BrowserKitTesting\TestCase` instead of `Illuminate\Foundation\Testing\TestCase`:

```php
<?php

namespace Tests;

use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public $baseUrl = 'http://localhost';

    // ...
}
```

No other modifications to your tests should be necessary.
