<?php

use BookStack\Http\Request;
use Illuminate\Contracts\Http\Kernel;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists(__DIR__ . '/../storage/framework/maintenance.php')) {
    require __DIR__ . '/../storage/framework/maintenance.php';
}

// Register the Composer autoloader...
require __DIR__ . '/../vendor/autoload.php';


// Run the application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->alias('request', Request::class);

$kernel = $app->make(Kernel::class);

$response = tap($kernel->handle(
    $request = Request::capture()
))->send();

$kernel->terminate($request, $response);
