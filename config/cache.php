<?php

// MEMCACHED - Split out configuration into an array
if (env('CACHE_DRIVER') === 'memcached') {
    $memcachedServerKeys = ['host', 'port', 'weight'];
    $memcachedServers = explode(',', trim(env('MEMCACHED_SERVERS', '127.0.0.1:11211:100'), ','));
    foreach ($memcachedServers as $index => $memcachedServer) {
        $memcachedServerDetails = explode(':', $memcachedServer);
        if (count($memcachedServerDetails) < 2) $memcachedServerDetails[] = '11211';
        if (count($memcachedServerDetails) < 3) $memcachedServerDetails[] = '100';
        $memcachedServers[$index] = array_combine($memcachedServerKeys, $memcachedServerDetails);
    }
}

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache connection that gets used while
    | using this caching library. This connection is used when another is
    | not explicitly specified when executing a given caching function.
    |
    */

    'default' => env('CACHE_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    */

    'stores' => [

        'apc' => [
            'driver' => 'apc',
        ],

        'array' => [
            'driver' => 'array',
        ],

        'database' => [
            'driver' => 'database',
            'table'  => 'cache',
            'connection' => null,
        ],

        'file' => [
            'driver' => 'file',
            'path'   => storage_path('framework/cache'),
        ],

        'memcached' => [
            'driver'  => 'memcached',
            'servers' => env('CACHE_DRIVER') === 'memcached' ? $memcachedServers : [],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing a RAM based store such as APC or Memcached, there might
    | be other applications utilizing the same cache. So, we'll specify a
    | value to get prefixed to all our keys so we can avoid collisions.
    |
    */

    'prefix' => env('CACHE_PREFIX', 'bookstack'),

];
