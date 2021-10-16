<?php

namespace BookStack\Auth\Access\Oidc;

use InvalidArgumentException;
use League\OAuth2\Client\Token\AccessToken;

class OidcAccessToken extends AccessToken
{
    /**
     * Constructs an access token.
     *
     * @param array $options An array of options returned by the service provider
     *                       in the access token request. The `access_token` option is required.
     *
     * @throws InvalidArgumentException if `access_token` is not provided in `$options`.
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->validate($options);
    }

    /**
     * Validate this access token response for OIDC.
     * As per https://openid.net/specs/openid-connect-basic-1_0.html#TokenOK.
     */
    private function validate(array $options): void
    {
        // access_token: REQUIRED. Access Token for the UserInfo Endpoint.
        // Performed on the extended class

        // token_type: REQUIRED. OAuth 2.0 Token Type value. The value MUST be Bearer, as specified in OAuth 2.0
        // Bearer Token Usage [RFC6750], for Clients using this subset.
        // Note that the token_type value is case-insensitive.
        if (strtolower(($options['token_type'] ?? '')) !== 'bearer') {
            throw new InvalidArgumentException('The response token type MUST be "Bearer"');
        }

        // id_token: REQUIRED. ID Token.
        if (empty($options['id_token'])) {
            throw new InvalidArgumentException('An "id_token" property must be provided');
        }
    }

    /**
     * Get the id token value from this access token response.
     */
    public function getIdToken(): string
    {
        return $this->getValues()['id_token'];
    }
}
