<?php

namespace BookStack\Auth\Access\OpenIdConnect;

class OpenIdConnectIdToken
{
    /**
     * @var array
     */
    protected $header;

    /**
     * @var array
     */
    protected $payload;

    /**
     * @var string
     */
    protected $signature;

    /**
     * @var array[]|string[]
     */
    protected $keys;

    /**
     * @var string
     */
    protected $issuer;

    /**
     * @var array
     */
    protected $tokenParts = [];

    public function __construct(string $token, string $issuer, array $keys)
    {
        $this->keys = $keys;
        $this->issuer = $issuer;
        $this->parse($token);
    }

    /**
     * Parse the token content into its components.
     */
    protected function parse(string $token): void
    {
        $this->tokenParts = explode('.', $token);
        $this->header = $this->parseEncodedTokenPart($this->tokenParts[0]);
        $this->payload = $this->parseEncodedTokenPart($this->tokenParts[1] ?? '');
        $this->signature = $this->base64UrlDecode($this->tokenParts[2] ?? '') ?: '';
    }

    /**
     * Parse a Base64-JSON encoded token part.
     * Returns the data as a key-value array or empty array upon error.
     */
    protected function parseEncodedTokenPart(string $part): array
    {
        $json = $this->base64UrlDecode($part) ?: '{}';
        $decoded = json_decode($json, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Base64URL decode. Needs some character conversions to be compatible
     * with PHP's default base64 handling.
     */
    protected function base64UrlDecode(string $encoded): string
    {
        return base64_decode(strtr($encoded, '-_', '+/'));
    }

    /**
     * Validate all possible parts of the id token.
     * @throws InvalidTokenException
     */
    public function validate(string $clientId)
    {
        $this->validateTokenStructure();
        $this->validateTokenSignature();
        $this->validateTokenClaims($clientId);
    }

    /**
     * Fetch a specific claim from this token.
     * Returns null if it is null or does not exist.
     * @return mixed|null
     */
    public function getClaim(string $claim)
    {
        return $this->payload[$claim] ?? null;
    }

    /**
     * Get all returned claims within the token.
     */
    public function claims(): array
    {
        return $this->payload;
    }

    /**
     * Validate the structure of the given token and ensure we have the required pieces.
     * As per https://datatracker.ietf.org/doc/html/rfc7519#section-7.2
     * @throws InvalidTokenException
     */
    protected function validateTokenStructure(): void
    {
        foreach (['header', 'payload'] as $prop) {
            if (empty($this->$prop) || !is_array($this->$prop)) {
                throw new InvalidTokenException("Could not parse out a valid {$prop} within the provided token");
            }
        }

        if (empty($this->signature) || !is_string($this->signature)) {
            throw new InvalidTokenException("Could not parse out a valid signature within the provided token");
        }
    }

    /**
     * Validate the signature of the given token and ensure it validates against the provided key.
     * @throws InvalidTokenException
     */
    protected function validateTokenSignature(): void
    {
        if ($this->header['alg'] !== 'RS256') {
            throw new InvalidTokenException("Only RS256 signature validation is supported. Token reports using {$this->header['alg']}");
        }

        $parsedKeys = array_map(function($key) {
            try {
                return new JwtSigningKey($key);
            } catch (InvalidKeyException $e) {
                return null;
            }
        }, $this->keys);

        $parsedKeys = array_filter($parsedKeys);

        $contentToSign = $this->tokenParts[0] . '.' . $this->tokenParts[1];
        /** @var JwtSigningKey $parsedKey */
        foreach ($parsedKeys as $parsedKey) {
            if ($parsedKey->verify($contentToSign, $this->signature)) {
                return;
            }
        }

        throw new InvalidTokenException('Token signature could not be validated using the provided keys');
    }

    /**
     * Validate the claims of the token.
     * As per https://openid.net/specs/openid-connect-basic-1_0.html#IDTokenValidation
     * @throws InvalidTokenException
     */
    protected function validateTokenClaims(string $clientId): void
    {
        // 1. The Issuer Identifier for the OpenID Provider (which is typically obtained during Discovery)
        // MUST exactly match the value of the iss (issuer) Claim.
        if (empty($this->payload['iss']) || $this->issuer !== $this->payload['iss']) {
            throw new InvalidTokenException('Missing or non-matching token issuer value');
        }

        // 2. The Client MUST validate that the aud (audience) Claim contains its client_id value registered
        // at the Issuer identified by the iss (issuer) Claim as an audience. The ID Token MUST be rejected
        // if the ID Token does not list the Client as a valid audience, or if it contains additional
        // audiences not trusted by the Client.
        if (empty($this->payload['aud'])) {
            throw new InvalidTokenException('Missing token audience value');
        }

        $aud = is_string($this->payload['aud']) ? [$this->payload['aud']] : $this->payload['aud'];
        if (count($aud) !== 1) {
            throw new InvalidTokenException('Token audience value has ' . count($aud) . ' values. Expected 1.');
        }

        if ($aud[0] !== $clientId) {
            throw new InvalidTokenException('Token audience value did not match the expected client_id');
        }

        // 3. If the ID Token contains multiple audiences, the Client SHOULD verify that an azp Claim is present.
        // NOTE: Addressed by enforcing a count of 1 above.

        // 4. If an azp (authorized party) Claim is present, the Client SHOULD verify that its client_id
        // is the Claim Value.
        if (isset($this->payload['azp']) && $this->payload['azp'] !== $clientId) {
            throw new InvalidTokenException('Token authorized party exists but does not match the expected client_id');
        }

        // 5. The current time MUST be before the time represented by the exp Claim
        // (possibly allowing for some small leeway to account for clock skew).
        if (empty($this->payload['exp'])) {
            throw new InvalidTokenException('Missing token expiration time value');
        }

        $skewSeconds = 120;
        $now = time();
        if ($now >= (intval($this->payload['exp']) + $skewSeconds)) {
            throw new InvalidTokenException('Token has expired');
        }

        // 6. The iat Claim can be used to reject tokens that were issued too far away from the current time,
        // limiting the amount of time that nonces need to be stored to prevent attacks.
        // The acceptable range is Client specific.
        if (empty($this->payload['iat'])) {
            throw new InvalidTokenException('Missing token issued at time value');
        }

        $dayAgo = time() - 86400;
        $iat = intval($this->payload['iat']);
        if ($iat > ($now + $skewSeconds) || $iat < $dayAgo) {
            throw new InvalidTokenException('Token issue at time is not recent or is invalid');
        }

        // 7. If the acr Claim was requested, the Client SHOULD check that the asserted Claim Value is appropriate.
        // The meaning and processing of acr Claim Values is out of scope for this document.
        // NOTE: Not used for our case here. acr is not requested.

        // 8. When a max_age request is made, the Client SHOULD check the auth_time Claim value and request
        // re-authentication if it determines too much time has elapsed since the last End-User authentication.
        // NOTE: Not used for our case here. A max_age request is not made.

        // Custom: Ensure the "sub" (Subject) Claim exists and has a value.
        if (empty($this->payload['sub'])) {
            throw new InvalidTokenException('Missing token subject value');
        }
    }

}