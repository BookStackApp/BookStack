<?php

return [

    // Display name, shown to users, for OpenId option
    'name' => env('OIDC_NAME', 'SSO'),

    // Dump user details after a login request for debugging purposes
    'dump_user_details' => env('OIDC_DUMP_USER_DETAILS', false),

    // Attribute, within a OpenId token, to find the user's display name
    'display_name_claims' => explode('|', env('OIDC_DISPLAY_NAME_CLAIMS', 'name')),

    // OAuth2/OpenId client id, as configured in your Authorization server.
    'client_id' => env('OIDC_CLIENT_ID', null),

    // OAuth2/OpenId client secret, as configured in your Authorization server.
    'client_secret' => env('OIDC_CLIENT_SECRET', null),

    // The issuer of the identity token (id_token) this will be compared with
    // what is returned in the token.
    'issuer' => env('OIDC_ISSUER', null),

    // Auto-discover the relevant endpoints and keys from the issuer.
    // Fetched details are cached for 15 minutes.
    'discover' => env('OIDC_ISSUER_DISCOVER', false),

    // Public key that's used to verify the JWT token with.
    // Can be the key value itself or a local 'file://public.key' reference.
    'jwt_public_key' => env('OIDC_PUBLIC_KEY', null),

    // OAuth2 endpoints.
    'authorization_endpoint' => env('OIDC_AUTH_ENDPOINT', null),
    'token_endpoint'         => env('OIDC_TOKEN_ENDPOINT', null),
];
