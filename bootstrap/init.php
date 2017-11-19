<?php

/*
|--------------------------------------------------------------------------
| Load Our Own Helpers
|--------------------------------------------------------------------------
|
| This custom function loads any helpers, before the Laravel Framework
| is built so we can override any helpers as we please.
|
*/
require __DIR__.'/../app/helpers.php';

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/
require __DIR__.'/../vendor/autoload.php';