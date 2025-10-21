<?php

$providerKeys = explode(',', env('OIDC_PROVIDERS', ''));

// Generate a multi-provider config array
$providers = collect($providerKeys)
    ->filter()
    ->mapWithKeys(function ($key) {
        $envPrefix = 'OIDC_' . strtoupper(trim($key)) . '_';

        return [strtolower($key) => [
            'name'                  => env($envPrefix . 'NAME', ucfirst($key)),
            'client_id'             => env($envPrefix . 'CLIENT_ID'),
            'client_secret'         => env($envPrefix . 'CLIENT_SECRET'),
            'issuer'                => env($envPrefix . 'ISSUER'),
            'discover'              => env($envPrefix . 'ISSUER_DISCOVER', false),
            'jwt_public_key'        => env($envPrefix . 'PUBLIC_KEY', null),
            'authorization_endpoint'=> env($envPrefix . 'AUTH_ENDPOINT', null),
            'token_endpoint'        => env($envPrefix . 'TOKEN_ENDPOINT', null),
            'userinfo_endpoint'     => env($envPrefix . 'USERINFO_ENDPOINT', null),
            'end_session_endpoint'  => env($envPrefix . 'END_SESSION_ENDPOINT', false),
            'additional_scopes'     => env($envPrefix . 'ADDITIONAL_SCOPES', null),
            'display_name_claims'   => env($envPrefix . 'DISPLAY_NAME_CLAIMS', 'name'),
            'external_id_claim'     => env($envPrefix . 'EXTERNAL_ID_CLAIM', 'sub'),
            'fetch_avatar'          => env($envPrefix . 'FETCH_AVATAR', false),
            'user_to_groups'        => env($envPrefix . 'USER_TO_GROUPS', false),
            'groups_claim'          => env($envPrefix . 'GROUPS_CLAIM', 'groups'),
            'remove_from_groups'    => env($envPrefix . 'REMOVE_FROM_GROUPS', false),
            'dump_user_details'     => env($envPrefix . 'DUMP_USER_DETAILS', false)
        ]];
    });

return [
    // Optional: Keep legacy single-provider fallback config
    'name' => env('OIDC_NAME', 'SSO'),
    'dump_user_details' => env('OIDC_DUMP_USER_DETAILS', false),

    // Default provider if needed
    'default' => env('OIDC_DEFAULT_PROVIDER', null),

    // Multiple provider configurations
    'providers' => $providers->toArray(),
];