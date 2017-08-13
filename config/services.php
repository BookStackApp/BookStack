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
    'disable_services' => env('DISABLE_EXTERNAL_SERVICES', false),
    'callback_url' => env('APP_URL', false),

    'mailgun'  => [
        'domain' => env('MAILGUN_DOMAIN', false),
        'secret' => env('MAILGUN_SECRET', false),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_SECRET', false),
    ],

    'ses'      => [
        'key'    => env('SES_KEY', false),
        'secret' => env('SES_SECRET', false),
        'region' => env('SES_REGION', false),
    ],

    'stripe'   => [
        'model'  => BookStack\User::class,
        'key'    => env('STRIPE_KEY', false),
        'secret' => env('STRIPE_SECRET', false),
    ],

    'github'   => [
        'client_id'     => env('GITHUB_APP_ID', false),
        'client_secret' => env('GITHUB_APP_SECRET', false),
        'redirect'      => env('APP_URL') . '/login/service/github/callback',
        'name'          => 'GitHub',
    ],

    'google'   => [
        'client_id'     => env('GOOGLE_APP_ID', false),
        'client_secret' => env('GOOGLE_APP_SECRET', false),
        'redirect'      => env('APP_URL') . '/login/service/google/callback',
        'name'          => 'Google',
    ],

    'slack'   => [
        'client_id'     => env('SLACK_APP_ID', false),
        'client_secret' => env('SLACK_APP_SECRET', false),
        'redirect'      => env('APP_URL') . '/login/service/slack/callback',
        'name'          => 'Slack',
    ],

    'facebook'   => [
        'client_id'     => env('FACEBOOK_APP_ID', false),
        'client_secret' => env('FACEBOOK_APP_SECRET', false),
        'redirect'      => env('APP_URL') . '/login/service/facebook/callback',
        'name'          => 'Facebook',
    ],

    'twitter'   => [
        'client_id'     => env('TWITTER_APP_ID', false),
        'client_secret' => env('TWITTER_APP_SECRET', false),
        'redirect'      => env('APP_URL') . '/login/service/twitter/callback',
        'name'          => 'Twitter',
    ],

    'ldap' => [
        'server' => env('LDAP_SERVER', false),
        'dn' => env('LDAP_DN', false),
        'pass' => env('LDAP_PASS', false),
        'base_dn' => env('LDAP_BASE_DN', false),
        'user_filter' => env('LDAP_USER_FILTER', '(&(uid=${user}))'),
        'version' => env('LDAP_VERSION', false),
        'email_attribute' => env('LDAP_EMAIL_ATTRIBUTE', 'mail'),
        'follow_referrals' => env('LDAP_FOLLOW_REFERRALS', false),
    ]

];
