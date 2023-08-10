<?php
return [
    "name" => (env('APP_NAME' | 'BookStack') ??'BookStack' ), 
    "short_name" => "bookstack", 
    "start_url" => "./", 
    "scope" => ".", 
    "display" => "standalone", 
    "background_color" => "#fff", 
    "description" =>( env('APP_NAME' | 'BookStack') ??'BookStack'), 
    "categories" => [
        "productivity", 
        "lifestyle" 
    ], 
    "launch_handler" => [
        "client_mode" => "focus-existing" 
    ], 
    "orientation" => "portrait", 
    "icons" => [
        [
            "src" => "/icon-64.png", 
            "sizes" => "64x64", 
            "type" => "image/png" 
        ], 
        [
            "src" => "/icon-32.png", 
            "sizes" => "32x32", 
            "type" => "image/png" 
        ], 
        [
            "src" => "/icon-128.png", 
            "sizes" => "128x128", 
            "type" => "image/png" 
        ], 
        [
            "src" => "icon-180.png", 
            "sizes" => "180x180", 
            "type" => "image/png" 
        ], 
        [
            "src" => "icon.png", 
            "sizes" => "256x256", 
            "type" => "image/png" 
        ], 
        [
            "src" => "icon.ico", 
            "sizes" => "48x48", 
            "type" => "image/vnd.microsoft.icon" 
        ], 
        [
            "src" => "favicon.ico", 
            "sizes" => "48x48", 
            "type" => "image/vnd.microsoft.icon" 
        ],
    ],
];