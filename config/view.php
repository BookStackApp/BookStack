<?php

$viewPaths = [realpath(base_path('resources/views'))];
if ($theme = env('APP_THEME', false)) {
    array_unshift($viewPaths, base_path('themes/' . $theme));
}

return [

    /*
    |--------------------------------------------------------------------------
    | App theme
    |--------------------------------------------------------------------------
    |
    | This option defines the theme to use for the application. When a theme
    | is set there mush be a `themes/<theme_name>` folder to hold the
    | custom theme overrides.
    |
    */

    'theme' => env('APP_THEME', false),

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'paths' => $viewPaths,

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => realpath(storage_path('framework/views')),

];
