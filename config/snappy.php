<?php

return [
    'pdf' => [
        'enabled' => true,
        'binary'  => file_exists(base_path('wkhtmltopdf')) ? base_path('wkhtmltopdf') : env('WKHTMLTOPDF', false),
        'timeout' => false,
        'options' => [],
        'env'     => [],
    ],
    'image' => [
        'enabled' => false,
        'binary'  => '/usr/local/bin/wkhtmltoimage',
        'timeout' => false,
        'options' => [],
        'env'     => [],
    ],
];
