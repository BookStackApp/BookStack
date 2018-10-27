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

    // Single option to disable non-auth external services such as Gravatar and Draw.io
    'disable_services' => env('DISABLE_EXTERNAL_SERVICES', false),
    'gravatar' => env('GRAVATAR', !env('DISABLE_EXTERNAL_SERVICES', false)),
    'drawio' => env('DRAWIO', !env('DISABLE_EXTERNAL_SERVICES', false)),


    'callback_url' => env('APP_URL', false),

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
        'model'  => \BookStack\Auth\User::class,
        'key'    => '',
        'secret' => '',
    ],

    'github'   => [
        'client_id'     => env('GITHUB_APP_ID', false),
        'client_secret' => env('GITHUB_APP_SECRET', false),
        'redirect'      => env('APP_URL') . '/login/service/github/callback',
        'name'          => 'GitHub',
        'auto_register' => env('GITHUB_AUTO_REGISTER', false),
        'auto_confirm' => env('GITHUB_AUTO_CONFIRM_EMAIL', false),
    ],

    'google'   => [
        'client_id'     => env('GOOGLE_APP_ID', false),
        'client_secret' => env('GOOGLE_APP_SECRET', false),
        'redirect'      => env('APP_URL') . '/login/service/google/callback',
        'name'          => 'Google',
        'auto_register' => env('GOOGLE_AUTO_REGISTER', false),
        'auto_confirm' => env('GOOGLE_AUTO_CONFIRM_EMAIL', false),
    ],

    'slack'   => [
        'client_id'     => env('SLACK_APP_ID', false),
        'client_secret' => env('SLACK_APP_SECRET', false),
        'redirect'      => env('APP_URL') . '/login/service/slack/callback',
        'name'          => 'Slack',
        'auto_register' => env('SLACK_AUTO_REGISTER', false),
        'auto_confirm' => env('SLACK_AUTO_CONFIRM_EMAIL', false),
    ],

    'facebook'   => [
        'client_id'     => env('FACEBOOK_APP_ID', false),
        'client_secret' => env('FACEBOOK_APP_SECRET', false),
        'redirect'      => env('APP_URL') . '/login/service/facebook/callback',
        'name'          => 'Facebook',
        'auto_register' => env('FACEBOOK_AUTO_REGISTER', false),
        'auto_confirm' => env('FACEBOOK_AUTO_CONFIRM_EMAIL', false),
    ],

    'twitter'   => [
        'client_id'     => env('TWITTER_APP_ID', false),
        'client_secret' => env('TWITTER_APP_SECRET', false),
        'redirect'      => env('APP_URL') . '/login/service/twitter/callback',
        'name'          => 'Twitter',
        'auto_register' => env('TWITTER_AUTO_REGISTER', false),
        'auto_confirm' => env('TWITTER_AUTO_CONFIRM_EMAIL', false),
    ],

    'azure'   => [
        'client_id'     => env('AZURE_APP_ID', false),
        'client_secret' => env('AZURE_APP_SECRET', false),
        'tenant'       => env('AZURE_TENANT', false),
        'redirect'      => env('APP_URL') . '/login/service/azure/callback',
        'name'          => 'Microsoft Azure',
        'auto_register' => env('AZURE_AUTO_REGISTER', false),
        'auto_confirm' => env('AZURE_AUTO_CONFIRM_EMAIL', false),
    ],

    'okta' => [
        'client_id' => env('OKTA_APP_ID'),
        'client_secret' => env('OKTA_APP_SECRET'),
        'redirect' => env('APP_URL') . '/login/service/okta/callback', 
        'base_url' => env('OKTA_BASE_URL'), 
        'name'          => 'Okta',
        'auto_register' => env('OKTA_AUTO_REGISTER', false),
        'auto_confirm' => env('OKTA_AUTO_CONFIRM_EMAIL', false),
    ],

    'gitlab' => [
        'client_id'     => env('GITLAB_APP_ID'),
        'client_secret' => env('GITLAB_APP_SECRET'),
        'redirect'      => env('APP_URL') . '/login/service/gitlab/callback',
        'instance_uri'  => env('GITLAB_BASE_URI'), // Needed only for self hosted instances
        'name'          => 'GitLab',
        'auto_register' => env('GITLAB_AUTO_REGISTER', false),
        'auto_confirm' => env('GITLAB_AUTO_CONFIRM_EMAIL', false),
    ],

    'twitch' => [
        'client_id' => env('TWITCH_APP_ID'),
        'client_secret' => env('TWITCH_APP_SECRET'),
        'redirect' => env('APP_URL') . '/login/service/twitch/callback',
        'name'          => 'Twitch',
        'auto_register' => env('TWITCH_AUTO_REGISTER', false),
        'auto_confirm' => env('TWITCH_AUTO_CONFIRM_EMAIL', false),
    ],

    'discord' => [
        'client_id' => env('DISCORD_APP_ID'),
        'client_secret' => env('DISCORD_APP_SECRET'),
        'redirect' => env('APP_URL') . '/login/service/discord/callback',
        'name' => 'Discord',
        'auto_register' => env('DISCORD_AUTO_REGISTER', false),
        'auto_confirm' => env('DISCORD_AUTO_CONFIRM_EMAIL', false),
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
		'user_to_groups' => env('LDAP_USER_TO_GROUPS',false),
		'group_attribute' => env('LDAP_GROUP_ATTRIBUTE', 'memberOf'),
		'remove_from_groups' => env('LDAP_REMOVE_FROM_GROUPS',false),
        'tls_insecure' => env('LDAP_TLS_INSECURE', false),
	]

];
