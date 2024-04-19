<?php

namespace BookStack\Access\Oidc;

class OidcIdToken extends OidcJwtWithClaims implements ProvidesClaims
{
    /**
     * Validate all possible parts of the id token.
     *
     * @throws OidcInvalidTokenException
     */
    public function validate(string $clientId): bool
    {
        parent::validateCommonTokenDetails($clientId);
        $this->validateTokenClaims($clientId);

        return true;
    }

    /**
     * Validate the claims of the token.
     * As per https://openid.net/specs/openid-connect-basic-1_0.html#IDTokenValidation.
     *
     * @throws OidcInvalidTokenException
     */
    protected function validateTokenClaims(string $clientId): void
    {
        // 1. The Issuer Identifier for the OpenID Provider (which is typically obtained during Discovery)
        // MUST exactly match the value of the iss (issuer) Claim.
        // Already done in parent.

        // 2. The Client MUST validate that the aud (audience) Claim contains its client_id value registered
        // at the Issuer identified by the iss (issuer) Claim as an audience. The ID Token MUST be rejected
        // if the ID Token does not list the Client as a valid audience, or if it contains additional
        // audiences not trusted by the Client.
        // Partially done in parent.
        $aud = is_string($this->payload['aud']) ? [$this->payload['aud']] : $this->payload['aud'];
        if (count($aud) !== 1) {
            throw new OidcInvalidTokenException('Token audience value has ' . count($aud) . ' values, Expected 1');
        }

        // 3. If the ID Token contains multiple audiences, the Client SHOULD verify that an azp Claim is present.
        // NOTE: Addressed by enforcing a count of 1 above.

        // 4. If an azp (authorized party) Claim is present, the Client SHOULD verify that its client_id
        // is the Claim Value.
        if (isset($this->payload['azp']) && $this->payload['azp'] !== $clientId) {
            throw new OidcInvalidTokenException('Token authorized party exists but does not match the expected client_id');
        }

        // 5. The current time MUST be before the time represented by the exp Claim
        // (possibly allowing for some small leeway to account for clock skew).
        if (empty($this->payload['exp'])) {
            throw new OidcInvalidTokenException('Missing token expiration time value');
        }

        $skewSeconds = 120;
        $now = time();
        if ($now >= (intval($this->payload['exp']) + $skewSeconds)) {
            throw new OidcInvalidTokenException('Token has expired');
        }

        // 6. The iat Claim can be used to reject tokens that were issued too far away from the current time,
        // limiting the amount of time that nonces need to be stored to prevent attacks.
        // The acceptable range is Client specific.
        if (empty($this->payload['iat'])) {
            throw new OidcInvalidTokenException('Missing token issued at time value');
        }

        $dayAgo = time() - 86400;
        $iat = intval($this->payload['iat']);
        if ($iat > ($now + $skewSeconds) || $iat < $dayAgo) {
            throw new OidcInvalidTokenException('Token issue at time is not recent or is invalid');
        }

        // 7. If the acr Claim was requested, the Client SHOULD check that the asserted Claim Value is appropriate.
        // The meaning and processing of acr Claim Values is out of scope for this document.
        // NOTE: Not used for our case here. acr is not requested.

        // 8. When a max_age request is made, the Client SHOULD check the auth_time Claim value and request
        // re-authentication if it determines too much time has elapsed since the last End-User authentication.
        // NOTE: Not used for our case here. A max_age request is not made.

        // Custom: Ensure the "sub" (Subject) Claim exists and has a value.
        if (empty($this->payload['sub'])) {
            throw new OidcInvalidTokenException('Missing token subject value');
        }
    }
}
