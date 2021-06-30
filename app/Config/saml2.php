<?php

$SAML2_IDP_AUTHNCONTEXT = env('SAML2_IDP_AUTHNCONTEXT', true);

return [

    // Display name, shown to users, for SAML2 option
    'name' => env('SAML2_NAME', 'SSO'),

    // Dump user details after a login request for debugging purposes
    'dump_user_details' => env('SAML2_DUMP_USER_DETAILS', false),

    // Attribute, within a SAML response, to find the user's email address
    'email_attribute' => env('SAML2_EMAIL_ATTRIBUTE', 'email'),
    // Attribute, within a SAML response, to find the user's display name
    'display_name_attributes' => explode('|', env('SAML2_DISPLAY_NAME_ATTRIBUTES', 'username')),
    // Attribute, within a SAML response, to use to connect a BookStack user to the SAML user.
    'external_id_attribute' => env('SAML2_EXTERNAL_ID_ATTRIBUTE', null),

    // Group sync options
    // Enable syncing, upon login, of SAML2 groups to BookStack groups
    'user_to_groups' => env('SAML2_USER_TO_GROUPS', false),
    // Attribute, within a SAML response, to find group names on
    'group_attribute' => env('SAML2_GROUP_ATTRIBUTE', 'group'),
    // When syncing groups, remove any groups that no longer match. Otherwise sync only adds new groups.
    'remove_from_groups' => env('SAML2_REMOVE_FROM_GROUPS', false),

    // Autoload IDP details from the metadata endpoint
    'autoload_from_metadata' => env('SAML2_AUTOLOAD_METADATA', false),

    // Overrides, in JSON format, to the configuration passed to underlying onelogin library.
    'onelogin_overrides' => env('SAML2_ONELOGIN_OVERRIDES', null),

    'onelogin' => [
        // If 'strict' is True, then the PHP Toolkit will reject unsigned
        // or unencrypted messages if it expects them signed or encrypted
        // Also will reject the messages if not strictly follow the SAML
        // standard: Destination, NameId, Conditions ... are validated too.
        'strict' => true,

        // Enable debug mode (to print errors)
        'debug' => env('APP_DEBUG', false),

        // Set a BaseURL to be used instead of try to guess
        // the BaseURL of the view that process the SAML Message.
        // Ex. http://sp.example.com/
        //     http://example.com/sp/
        'baseurl' => null,

        // Service Provider Data that we are deploying
        'sp' => [
            // Identifier of the SP entity  (must be a URI)
            'entityId' => '',

            // Specifies info about where and how the <AuthnResponse> message MUST be
            // returned to the requester, in this case our SP.
            'assertionConsumerService' => [
                // URL Location where the <Response> from the IdP will be returned
                'url' => '',
                // SAML protocol binding to be used when returning the <Response>
                // message.  Onelogin Toolkit supports for this endpoint the
                // HTTP-POST binding only
                'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
            ],

            // Specifies info about where and how the <Logout Response> message MUST be
            // returned to the requester, in this case our SP.
            'singleLogoutService' => [
                // URL Location where the <Response> from the IdP will be returned
                'url' => '',
                // SAML protocol binding to be used when returning the <Response>
                // message.  Onelogin Toolkit supports for this endpoint the
                // HTTP-Redirect binding only
                'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
            ],

            // Specifies constraints on the name identifier to be used to
            // represent the requested subject.
            // Take a look on lib/Saml2/Constants.php to see the NameIdFormat supported
            'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
            // Usually x509cert and privateKey of the SP are provided by files placed at
            // the certs folder. But we can also provide them with the following parameters
            'x509cert'   => '',
            'privateKey' => '',
        ],
        // Identity Provider Data that we want connect with our SP
        'idp' => [
            // Identifier of the IdP entity  (must be a URI)
            'entityId' => env('SAML2_IDP_ENTITYID', null),
            // SSO endpoint info of the IdP. (Authentication Request protocol)
            'singleSignOnService' => [
                // URL Target of the IdP where the SP will send the Authentication Request Message
                'url' => env('SAML2_IDP_SSO', null),
                // SAML protocol binding to be used when returning the <Response>
                // message.  Onelogin Toolkit supports for this endpoint the
                // HTTP-Redirect binding only
                'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
            ],
            // SLO endpoint info of the IdP.
            'singleLogoutService' => [
                // URL Location of the IdP where the SP will send the SLO Request
                'url' => env('SAML2_IDP_SLO', null),
                // URL location of the IdP where the SP will send the SLO Response (ResponseLocation)
                // if not set, url for the SLO Request will be used
                'responseUrl' => null,
                // SAML protocol binding to be used when returning the <Response>
                // message.  Onelogin Toolkit supports for this endpoint the
                // HTTP-Redirect binding only
                'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
            ],
            // Public x509 certificate of the IdP
            'x509cert' => env('SAML2_IDP_x509', null),
            /*
             *  Instead of use the whole x509cert you can use a fingerprint in
             *  order to validate the SAMLResponse, but we don't recommend to use
             *  that method on production since is exploitable by a collision
             *  attack.
             *  (openssl x509 -noout -fingerprint -in "idp.crt" to generate it,
             *   or add for example the -sha256 , -sha384 or -sha512 parameter)
             *
             *  If a fingerprint is provided, then the certFingerprintAlgorithm is required in order to
             *  let the toolkit know which Algorithm was used. Possible values: sha1, sha256, sha384 or sha512
             *  'sha1' is the default value.
             */
            // 'certFingerprint' => '',
            // 'certFingerprintAlgorithm' => 'sha1',
            /* In some scenarios the IdP uses different certificates for
             * signing/encryption, or is under key rollover phase and more
             * than one certificate is published on IdP metadata.
             * In order to handle that the toolkit offers that parameter.
             * (when used, 'x509cert' and 'certFingerprint' values are
             * ignored).
             */
            // 'x509certMulti' => array(
            //      'signing' => array(
            //          0 => '<cert1-string>',
            //      ),
            //      'encryption' => array(
            //          0 => '<cert2-string>',
            //      )
            // ),
        ],
        'security' => [
            // SAML2 Authn context
            // When set to false no AuthContext will be sent in the AuthNRequest,
            // When set to true (Default) you will get an AuthContext 'exact' 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport'.
            // Multiple forced values can be passed via a space separated array, For example:
            // SAML2_IDP_AUTHNCONTEXT="urn:federation:authentication:windows urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport"
            'requestedAuthnContext' => is_string($SAML2_IDP_AUTHNCONTEXT) ? explode(' ', $SAML2_IDP_AUTHNCONTEXT) : $SAML2_IDP_AUTHNCONTEXT,
        ],
    ],

];
