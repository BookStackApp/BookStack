<?php

return [

    // Display name, shown to users, for OpenId option
    'name' => env('OPENID_NAME', 'SSO'),

    // Dump user details after a login request for debugging purposes
    'dump_user_details' => env('OPENID_DUMP_USER_DETAILS', false),

    // Attribute, within a OpenId token, to find the user's email address
    'email_attribute' => env('OPENID_EMAIL_ATTRIBUTE', 'email'),
    // Attribute, within a OpenId token, to find the user's display name
    'display_name_attributes' => explode('|', env('OPENID_DISPLAY_NAME_ATTRIBUTES', 'username')),
    // Attribute, within a OpenId token, to use to connect a BookStack user to the OpenId user.
    'external_id_attribute' => env('OPENID_EXTERNAL_ID_ATTRIBUTE', null),

    // Overrides, in JSON format, to the configuration passed to underlying OpenIDConnectProvider library.
    'openid_overrides' => env('OPENID_OVERRIDES', null),

    'openid' => [
        // OAuth2/OpenId client id, as configured in your Authorization server.
        'clientId'                => env('OPENID_CLIENT_ID', ''),

        // OAuth2/OpenId client secret, as configured in your Authorization server.
        'clientSecret'            => env('OPENID_CLIENT_SECRET', ''),

        // OAuth2 scopes that are request, by default the OpenId-native profile and email scopes.
        'scopes'                  => 'profile email',

        // The issuer of the identity token (id_token) this will be compared with what is returned in the token.
        'idTokenIssuer'           => env('OPENID_ISSUER', ''),

        // Public key that's used to verify the JWT token with.
        'publicKey'               => env('OPENID_PUBLIC_KEY', ''),

        // OAuth2 endpoints.
        'urlAuthorize'            => env('OPENID_URL_AUTHORIZE', ''),
        'urlAccessToken'          => env('OPENID_URL_TOKEN', ''),
        'urlResourceOwnerDetails' => env('OPENID_URL_RESOURCE', ''),
    ],

];
