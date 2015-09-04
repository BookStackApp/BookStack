<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun'  => [
        'domain' => '',
        'secret' => '',
    ],

    'mandrill' => [
        'secret' => '',
    ],

    'ses'      => [
        'key'    => '',
        'secret' => '',
        'region' => 'us-east-1',
    ],

    'stripe'   => [
        'model'  => Oxbow\User::class,
        'key'    => '',
        'secret' => '',
    ],

    'github'   => [
        'client_id'     => env('GITHUB_APP_ID', false),
        'client_secret' => env('GITHUB_APP_SECRET', false),
        'redirect'      => env('APP_URL') . '/login/service/github/callback',
    ],

    'google'   => [
        'client_id'     => env('GOOGLE_APP_ID', false),
        'client_secret' => env('GOOGLE_APP_SECRET', false),
        'redirect'      => env('APP_URL') . '/login/service/google/callback',
    ],

];
